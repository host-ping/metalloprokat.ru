function ownWindow() {
    this.inputContainer = document.createElement('DIV');
    this.inputContainer.id = "cookie-div";
    this.inputContainer.style= 'padding: 5px 0 0 10px; border-radius: 10px; display: block; top: 0; position: absolute; z-index: 999; background-color: #87CEFA; height: 100px; width:155px';

    this.p1 = document.createElement('P');
    this.checkbox1 = document.createElement('INPUT');
    this.checkbox1.type = 'checkbox';
    this.checkbox1.id = 'dev-env';
    this.labelCheckbox1 = document.createTextNode(' dev_env');
    this.p1.appendChild(this.checkbox1);
    this.p1.appendChild(this.labelCheckbox1);

    this.p2 = document.createElement('P');
    this.checkbox2 = document.createElement('INPUT');
    this.checkbox2.type = 'checkbox';
    this.checkbox2.id = 'debug-mode';
    this.labelCheckbox2 = document.createTextNode(' debug_mode');
    this.p2.appendChild(this.checkbox2);
    this.p2.appendChild(this.labelCheckbox2);

    this.p3 = document.createElement('P');
    this.checkbox3 = document.createElement('INPUT');
    this.checkbox3.type = 'checkbox';
    this.checkbox3.id = 'shutdown-function';
    this.labelCheckbox3 = document.createTextNode(' shutdown_function');
    this.p3.appendChild(this.checkbox3);
    this.p3.appendChild(this.labelCheckbox3);

    this.button = document.createElement('INPUT');
    this.button.type = 'submit';
    this.button.id = 'cookie-submit';
    this.button.value = 'выбрать';
    this.button.style = 'display: block; border-radius: 5px; width: 75px; background-color: #E0FFFF;';

    this.inputContainer.appendChild(this.p1);
    this.inputContainer.appendChild(this.p2);
    this.inputContainer.appendChild(this.p3);
    this.inputContainer.appendChild(this.button);

    document.getElementById('main').appendChild(this.inputContainer);

    document.getElementById('cookie-submit').onclick =  function() {

        var cookie = '';

        if (document.getElementById('dev-env').checked) {
            cookie = 'ioniatte ';
        }

        if (document.getElementById('debug-mode').checked) {
            cookie += 'gabryoka ';
        }

        if (document.getElementById('shutdown-function').checked) {
            cookie += 'ousterra';
        }

        setCookie('debug_key', cookie);

        document.getElementById('cookie-div').remove();

    }

}

function setCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires*1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for(var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}
