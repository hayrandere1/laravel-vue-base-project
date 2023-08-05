<template>
    <div>
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
                <div class="text-subtitle-1 text-medium-emphasis">Account</div>

                <v-text-field
                    density="compact"
                    placeholder="Username"
                    prepend-inner-icon="mdi-account-outline"
                    variant="outlined"
                    :error-messages="form.errors.username"
                    v-model="form.username"
                    tabindex="1"
                ></v-text-field>

                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                    Password
                    <a
                        class="text-caption text-decoration-none text-blue"
                        :href="route('user.password.request')"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        Forgot login password?</a>
                </div>

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
                    Log In
                </v-btn>

                <v-card-text class="text-center">
                    <a
                        class="text-blue text-decoration-none"
                        href="#"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        Sign up now
                        <v-icon icon="mdi-chevron-right"></v-icon>
                    </a>
                </v-card-text>
            </v-card>
        </form>
    </div>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "Login",
    data() {
        return {
            visible: false,
            form: useForm({
                username: '',
                password: '',
                remember: false,
            }),
        }
    },
    methods: {
        submit() {
            this.form.post(route('user.login'));
        }
    }
}
</script>

<style scoped>

</style>
