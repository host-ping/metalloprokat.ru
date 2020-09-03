/**
 * This file copyright (C) 2015 Konstantin Myakshin (koc-dp@yandex.ru)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

///////////////
// A Sphinx UDF `increasepriority(flag, unique, limit)` that returns `1` no more than `limit` times once per `unique` when `flag` is not false
// http://sphinxsearch.com/docs/current.html#udf
// gcc -fPIC -shared -o increasepriority.so increasepriority.cpp
// CREATE FUNCTION increasepriority RETURNS INT SONAME 'increasepriority.so';
///////////////

#include "sphinxudf.h"
#include <cstdio>
#include <map>
#include <string>
#include <stdbool.h>

// Could use std::unordered_map in C++11, but let's be more portable:
typedef std::map<unsigned int,unsigned int> IntCount;

#ifdef _MSC_VER
#define snprintf _snprintf
#define DLLEXPORT __declspec(dllexport)
#else
#define DLLEXPORT
#endif

extern "C" {
    DLLEXPORT int increasepriority_ver();

    DLLEXPORT int increasepriority_init(SPH_UDF_INIT*, SPH_UDF_ARGS*, char*);
    DLLEXPORT void increasepriority_deinit (SPH_UDF_INIT*);
    DLLEXPORT bool increasepriority(SPH_UDF_INIT*, SPH_UDF_ARGS*, char*);
}

/// UDF version control
/// gets called once when the library is loaded
DLLEXPORT int increasepriority_ver()
{
        return SPH_UDF_VERSION;
}

/// UDF initialization
/// gets called on every query, when query begins
/// args are filled with values for a particular query
DLLEXPORT int increasepriority_init(SPH_UDF_INIT *init,
                                SPH_UDF_ARGS *args,
                                char *error_message)
{
    // check argument count
    if (args->arg_count != 3) {
        snprintf(error_message, SPH_UDF_ERROR_LEN, "increasepriority() takes 3 arguments");
        return 1;
    }

    // check argument types
    if (args->arg_types[0] != SPH_UDF_TYPE_UINT32 && args->arg_types[0] != SPH_UDF_TYPE_INT64) {
        snprintf(error_message, SPH_UDF_ERROR_LEN, "increasepriority() requires 1st argument to be uint");
        return 1;
    }

    if (args->arg_types[1] != SPH_UDF_TYPE_UINT32 && args->arg_types[1] != SPH_UDF_TYPE_INT64) {
        snprintf(error_message, SPH_UDF_ERROR_LEN, "increasepriority() requires 2nd argument to be uint");
        return 1;
    }

    if (args->arg_types[2] != SPH_UDF_TYPE_UINT32 && args->arg_types[1] != SPH_UDF_TYPE_INT64) {
        snprintf(error_message, SPH_UDF_ERROR_LEN, "increasepriority() requires 3rd argument to be uint");
        return 1;
    }

    init->func_data = new IntCount();

    // all done
    return 0;
}

/// UDF deinitialization
/// gets called on every query, when query ends
DLLEXPORT void increasepriority_deinit(SPH_UDF_INIT *init)
{
    // deallocate storage
    if (init->func_data) {
        IntCount *m = static_cast<IntCount*>(init->func_data);
        delete m;
        init->func_data = NULL;
    }
}

/// UDF implementation
/// gets called for every row, unless optimized away
DLLEXPORT bool increasepriority(SPH_UDF_INIT *init, SPH_UDF_ARGS *args, char *)
{
    unsigned int flag = *(unsigned int*)args->arg_values[0];

    if (!flag) {
        return 0;
    }

    unsigned int unique = *(unsigned int*)args->arg_values[1];
    unsigned int limit = *(unsigned int*)args->arg_values[2];

    IntCount *m = static_cast<IntCount*>(init->func_data);

    if ((*m).size() >= limit) {
        return 0;
    }

    if ((*m)[unique] > 0) {
        return 0;
    }

    ++(*m)[unique];

    return 1;
}
