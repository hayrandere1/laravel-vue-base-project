<template>
    <v-img
        class="mx-auto my-6"
        max-width="228"
        src="https://cdn.vuetifyjs.com/docs/images/logos/vuetify-logo-v3-slim-text-light.svg"
    ></v-img>
    <v-card
        class="mx-auto pa-12 pb-8"
        elevation="8"
        max-width="448"
        rounded="lg"
    >
        <div class="text-subtitle-1 text-medium-emphasis">Account Forgot</div>

        <form @submit.prevent="submit">
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
                Verification Email
            </v-btn>
        </form>


        <form @submit.prevent="submit2">
            <v-btn
                block
                class="mb-8"
                color="red"
                size="large"
                variant="tonal"
                type="submit"
                :disabled="form2.processing"
                :loading="form2.processing"
            >
                Logout
            </v-btn>
        </form>

    </v-card>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "VerifyEmail",
    data() {
        return {
            form: useForm({}),
            form2: useForm({}),
        }
    },
    methods: {
        submit() {
            this.form.post(route('user.verification.resend'));
        },
        submit2() {
            this.form2.post(route('user.logout'));
        }
    }
}
</script>

<style scoped>

</style>
