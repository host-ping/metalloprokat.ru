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
// A Sphinx UDF `disorderly(seed = null)` to return a random number for a given `seed`
// http://sphinxsearch.com/docs/current.html#udf
// gcc -fPIC -shared -o disorderly.so disorderly.cpp
// CREATE FUNCTION disorderly RETURNS INT SONAME 'disorderly.so';
///////////////

#include "sphinxudf.h"

#include <stdio.h>      /* printf, NULL */
#include <stdlib.h>     /* srand, rand */
#include <time.h>
#include <stdbool.h>

#ifdef _MSC_VER
#define snprintf _snprintf
#define DLLEXPORT __declspec(dllexport)
#else
#define DLLEXPORT
#endif

extern "C" {
    DLLEXPORT int disorderly_ver();

    DLLEXPORT int disorderly_init(SPH_UDF_INIT*, SPH_UDF_ARGS*, char*);
    DLLEXPORT void disorderly_deinit (SPH_UDF_INIT*);
    DLLEXPORT sphinx_int64_t disorderly(SPH_UDF_INIT*, SPH_UDF_ARGS*, char*);
}


/// UDF version control
/// gets called once when the library is loaded
DLLEXPORT int disorderly_ver()
{
    return SPH_UDF_VERSION;
}

//////////////////////////////////////////////////////
/// UDF initialization
/// gets called on every query, when query begins
/// args are filled with values for a particular query
DLLEXPORT int disorderly_init(SPH_UDF_INIT *init, SPH_UDF_ARGS *args, char *error_message)
{
    // check argument count
    if (args->arg_count > 1) {
        snprintf(error_message, SPH_UDF_ERROR_LEN, "DISORDERLY() takes either 0 or 1 arguments");

        return 1;
    }

    // check argument types
    if (args->arg_count && args->arg_types[0] != SPH_UDF_TYPE_UINT32) {
        snprintf(error_message, SPH_UDF_ERROR_LEN, "DISORDERLY() requires 1st argument to be uint");

        return 1;
    }

    // store flag that srand was not called yet
    init->func_data = malloc(sizeof(bool));
    *(bool*)init->func_data = false;

    // all done
    return 0;
}

/// UDF deinitialization
/// gets called on every query, when query ends
DLLEXPORT void disorderly_deinit(SPH_UDF_INIT *init)
{
    // deallocate storage
    if (init->func_data) {
        free(init->func_data);
        init->func_data = NULL;
    }
}

/// UDF implementation
/// gets called for every row, unless optimized away
DLLEXPORT sphinx_int64_t disorderly(SPH_UDF_INIT *init, SPH_UDF_ARGS *args, char *)
{
    bool called = *(bool*)init->func_data;

    if (!called) {
        if (args->arg_count) {
            srand(*(int*)args->arg_values[0]);
        } else {
            srand(time(NULL));
        }
        *(bool*)init->func_data = true;
    }

    return rand();
}
