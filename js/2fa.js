import Lit from "litstack/resources/js/common/lit";
import LitTwoFA from "./LitTwoFA";

Lit.booting(Vue => {
    Vue.component("lit-two-fa", LitTwoFA);
});
