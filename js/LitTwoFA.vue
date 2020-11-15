<template>
    <lit-col class="mb-2">
        <template v-if="!model.two_fa_enabled">
            <div class="d-flex justify-content-between">
                <div class="" style="line-height: 100px;">
                    <i
                        class="fas fa-shield-alt text-secondary"
                        style="font-size: 3em;"
                    ></i>
                </div>
                <span class="ml-3">
                    Mit Zwei-Faktor-Authentifizierung kannst ausschließlich du
                    auf dein Konto zugreifen – selbst, wenn jemand anders im
                    Besitz deines Passworts ist.
                    <a
                        href="https://www.youtube.com/watch?v=0mvCeNsTa1g"
                        target="_blank"
                    >
                        Mehr erfahren.
                    </a>
                </span>
            </div>

            <h6 class="mb-3">WIE ES FUNKTIONIERT</h6>
            <p>Wenn du dich einloggst, musst du:</p>
            <ol>
                <li>Dein passwort eingeben</li>
                <li>
                    einen Code eingeben, der von einer App oder einer SMS
                    generiert wurde
                </li>
            </ol>
            <b-button variant="primary" @click="request2FA()" class="mt-3 mb-3">
                {{ __("2fa.activate").capitalizeAll() }}
            </b-button>
            <b-modal
                id="2fa-modal"
                size="lg"
                ok-title="Activate"
                @ok="activate"
            >
                <template #modal-title>
                    {{ __("2fa.activate").capitalizeAll() }}
                </template>
                <b-list-group>
                    <b-list-group-item>
                        <h6 class="mb-3">1. AUTHENTICATOR-APP INSTALLIEREN</h6>

                        <p>
                            Installiere bitte eine Authentication App, z.B:
                        </p>

                        <ul>
                            <li>
                                <lit-fa-icon :icon="['fab', 'apple']" />
                                <a
                                    href="https://apps.apple.com/de/app/2fa-authenticator-2fas"
                                    target="_blank"
                                >
                                    2FA Authenticator (2FAS)
                                </a>
                            </li>
                            <li>
                                <lit-fa-icon :icon="['fab', 'android']" />
                                <a
                                    href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=de&gl=US"
                                    target="_blank"
                                >
                                    Google Authenticator
                                </a>
                            </li>
                        </ul>
                    </b-list-group-item>
                    <b-list-group-item>
                        <h6 class="mb-3">2. APP KONFIGURIEREN</h6>

                        <div class="d-flex justify-content-between">
                            <div>
                                <p>
                                    Öffne die Authenticator-App auf deinem
                                    Mobilgerät und scanne den QR-Code.
                                </p>
                                <p>
                                    Du kannst den QR-Code nicht scannen? <br />
                                    Konfiguriere deine App mit diesem Schlüssel:
                                    <strong>{{ secret }}</strong>
                                </p>
                            </div>
                            <div v-html="qr"></div>
                        </div>
                    </b-list-group-item>
                    <b-list-group-item>
                        <h6 class="mb-3">3. GIB DEIN AKTUELLES PASSWORT EIN</h6>

                        <p>
                            Gib das Passwort ein, mit dem du dich derzeit
                            anmeldest.
                        </p>

                        <b-form-group
                            id="lit-2fa-password"
                            label="Current Password"
                            label-for="lit-2fa-password"
                        >
                            <b-form-input
                                id="lit-2fa-password"
                                v-model="form.password"
                                type="password"
                                :state="
                                    state.password.length == 0 ? null : false
                                "
                                placeholder="***"
                            ></b-form-input>
                            <b-form-invalid-feedback
                                v-for="(error, key) in state.password"
                                :key="key"
                            >
                                {{ error }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-list-group-item>

                    <b-list-group-item>
                        <h6 class="mb-3">
                            4. GIB DEN SECHSSTELLIGEN CODE EIN, DER VON DER APP
                            GENERIERT WURDE
                        </h6>

                        <b-form-group
                            id="lit-2fa-code"
                            label="Authenticationcode"
                            label-for="lit-2fa-code"
                        >
                            <b-form-input
                                id="lit-2fa-code"
                                v-model="form.code"
                                :state="state.code.length == 0 ? null : false"
                            ></b-form-input>
                            <b-form-invalid-feedback
                                v-for="(error, key) in state.code"
                                :key="key"
                            >
                                {{ error }}
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-list-group-item>
                </b-list-group>
            </b-modal>
        </template>
        <template v-else>
            <div class="mb-3">
                <span>
                    <i class="fas fa-check text-success mr-2"></i> Dein Konto
                    ist durch Zwei Faktor Authentifizierung geschützt.
                </span>
            </div>
        </template>
    </lit-col>
</template>

<script>
export default {
    name: "LitTwoFA",
    props: {
        model: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            qr: null,
            secret: null,
            form: {
                password: null,
                code: null
            },
            state: {
                password: [],
                code: []
            }
        };
    },
    methods: {
        async request2FA() {
            //$bvModal.show('2fa-modal')
            let response;
            try {
                response = await axios.post("2fa/request");
            } catch (e) {
                console.log(e);
                return;
            }

            this.qr = response.data.qr;
            this.secret = response.data.secret;

            this.$bvModal.show("2fa-modal");
        },
        async activate(e) {
            e.preventDefault();

            let response;
            try {
                response = await axios.post("2fa/activate", this.form);
            } catch (e) {
                this.handleActivationFailure(e);
                return;
            }

            this.state.password = [];
            this.state.code = [];
        },
        handleActivationFailure(error) {
            this.state.password = error.response.data.errors.password || [];
            this.state.code = error.response.data.errors.code || [];
        }
    }
};
</script>
