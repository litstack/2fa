import Lit from 'litstack/resources/js/common/lit';
import LitTwoFA from './LitTwoFA';
import LitFieldVerify from './LitFieldVerify';

Lit.booting((Vue) => {
	Vue.component('lit-two-fa', LitTwoFA);
	Vue.component('lit-field-verify', LitFieldVerify);
});
