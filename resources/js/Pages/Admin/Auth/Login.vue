<template>
    <div>
        <v-img
            class="mx-auto my-6"
            max-width="228"
            src="https://cdn.vuetifyjs.com/docs/images/logos/vuetify-logo-v3-slim-text-light.svg"
        ></v-img>

        <form @submit.prevent="submit">
            <v-locale-provider locale="tr">
                <v-card
                    class="mx-auto pa-12 pb-8"
                    elevation="8"
                    max-width="448"
                    rounded="lg"
                >
                    <div class="text-subtitle-1 text-medium-emphasis">Admin</div>
                    <v-text-field
                        density="compact"
                        placeholder="Username"
                        prepend-inner-icon="mdi-account-outline"
                        variant="outlined"
                        validate-on="blur"
                        :rules="rules.username"
                        :error-messages="form.errors.username"
                        v-model="form.username"
                        tabindex="1"
                        required
                        :counter="6"
                    ></v-text-field>

                    <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                        Password
                        <a
                            class="text-caption text-decoration-none text-blue"
                            :href="route('admin.password.request')"
                            rel="noopener noreferrer"
                            target="_blank"
                        >
                            Forgot login password?</a>
                    </div>

                    <v-text-field
                        :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                        :type="visible ? 'text' : 'password'"
                        density="compact"
                        required
                        placeholder="Enter your password"
                        prepend-inner-icon="mdi-lock-outline"
                        variant="outlined"
                        v-model="form.password"
                        :rules="rules.password"
                        :error-messages="form.errors.password"
                        @click:append-inner="visible = !visible"
                        tabindex="2"
                        :counter="8"
                        validate-on="blur"
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
            </v-locale-provider>
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
            rules: {
                username: [
                    value => {
                        if (value) return true
                        return 'Username is required.'
                    },
                    value => {
                        if (value.length >= 5) return true
                        return 'Username must be less than 6 characters.'
                    },
                ],
                password: [
                    value => {
                        if (value) return true
                        return 'Password is required.'
                    },
                    value => {
                        if (value.length >= 8) return true
                        return 'Password must be less than 8 characters.'
                    },
                ]
            },
            form: useForm({
                username: '',
                password: '',
                remember: false,
            }),
        }
    },
    methods: {
        submit() {
            this.form.post(route('admin.login'));
        }
    }
}
</script>

<style scoped>

</style>
