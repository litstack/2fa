import LitTwoFA from './LitTwoFA';
import LitFieldVerify from './LitFieldVerify';

console.log('Hi');

Lit.booting((Vue) => {
	Vue.component('lit-two-fa', LitTwoFA);
	Vue.component('lit-field-verify', LitFieldVerify);
});
