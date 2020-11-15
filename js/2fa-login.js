window.doLogin = function(e) {
    e.preventDefault();

    const data = new FormData(document.forms.login);

    let promise = axios.post(loginRoute, data);
    promise.then(function(response) {
        window.location = response.data;
    });
    promise.catch(function(error) {
        let has2FAError = !!error.response.data?.errors?.code;
        let firstAttempt = !document.getElementById("code");

        if (!has2FAError) {
            document.querySelector(".code-form-group")?.remove();
        }

        if (has2FAError && firstAttempt) {
            document.getElementById("login-failed").style.display = "none";
            let fields = document.querySelector(".form-fields");
            fields.insertAdjacentHTML(
                "beforeend",
                `
            <div class="form-group mb-3 mt-5 code-form-group">
                            <input 
                                placeholder="Code" 
                                id="code"
                                class="form-control lit-login-form" 
                                name="code" 
                                required 
                                />
                        </div>`
            );
        }

        if (!has2FAError || !firstAttempt) {
            document.getElementById("login-failed").style.display = "block";
            document.getElementById("forgot-password").style.display = "block";
        }
    });
};
