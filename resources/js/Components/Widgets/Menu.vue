<template>
    <div class="w-100 h-100">
        <v-card variant="tonal" :color="'#0d8995'">
            <v-list>
                <v-list-item
                    title="Home"
                    prepend-icon="mdi-home"
                    value="home"
                ></v-list-item>
                <v-list-item
                    title="Account"
                    prepend-icon="mdi-account"
                    value="account"
                ></v-list-item>
                <v-list-item
                    title="About Us"
                    prepend-icon="mdi-information-variant"
                    value="about us"
                ></v-list-item>
                <v-list-item
                    title="References"
                    prepend-icon="mdi-account-group"
                    value="references"
                ></v-list-item>
                <v-list-item
                    title="Contact"
                    prepend-icon="mdi-card-account-mail"
                    value="contact"
                ></v-list-item>
            </v-list>
        </v-card>
    </div>
</template>

<script>
export default {
    name: "Menu"
}
</script>

<style scoped>

</style>
