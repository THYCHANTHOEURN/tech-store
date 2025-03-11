import { Field, Form } from "vee-validate";

const register = (app) => {
    app.component("vee-form", Form);
    app.component("vee-field", Field);
};

export default {
    register,
};
