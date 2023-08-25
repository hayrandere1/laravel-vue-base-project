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
            <div class="text-subtitle-1 text-medium-emphasis">Account Reset</div>
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
            <v-text-field
                :append-inner-icon="visible2 ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible2 ? 'text' : 'password'"
                density="compact"
                placeholder="Check your password"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                v-model="form.password_confirmation"
                @click:append-inner="visible2 = !visible2"
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
                Reset
            </v-btn>
        </v-card>
    </form>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "ResetPassword.vue",
    data() {
        return {
            visible: false,
            visible2: false,
            form: useForm({
                token: this.token,
                username: this.username,
                password: '',
                password_confirmation: '',
            }),
        }
    },
    props: {
        username: String,
        token: String,
    },
    methods: {
        submit() {
            this.form.post(route('manager.password.update'));
        }
    }
}
</script>

<style scoped>

</style>
