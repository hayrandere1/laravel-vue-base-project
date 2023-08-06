<template>
    <v-img
        class="mx-auto my-6"
        max-width="228"
        src="https://cdn.vuetifyjs.com/docs/images/logos/vuetify-logo-v3-slim-text-light.svg"
    ></v-img>
    <form @submit.prevent="submit">
        <v-card
            class="mx-auto pa-12 pb-8"
            elevation="8"
            max-width="448"
            rounded="lg"
        >
            <div class="text-subtitle-1 text-medium-emphasis">Confirm Password</div>
            <v-text-field
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                density="compact"
                placeholder="Enter your password"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                v-model="form.password"
                @click:append-inner="visible = !visible"
                tabindex="2"
            ></v-text-field>
            <v-btn
                block
                class="mb-8"
                color="blue"
                size="large"
                variant="tonal"
                type="submit"
                :disabled="form.processing"
                :loading="form.processing"
            >
                Forgot
            </v-btn>
        </v-card>
    </form>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "PasswordsConfirm",
    data() {
        return {
            visible: false,
            form: useForm({
                password: '',
            }),
        }
    },
    methods: {
        submit() {
            this.form.post(route('user.password.confirm'));
        }
    }
}
</script>

<style scoped>

</style>
