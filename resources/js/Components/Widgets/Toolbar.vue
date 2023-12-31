<template>
    <v-app>
        <v-app-bar app :color="'#0d8995'">
            <!-- Sol Tarafta Logo veya Şirket Adı -->
<!--            <v-app-bar-nav-icon></v-app-bar-nav-icon>-->
            <v-toolbar-title>Hyppery</v-toolbar-title>

            <!-- Sağ Tarafta Button Ikonları -->
            <v-btn icon @click="handleButtonClick">
                <v-icon>mdi-bell</v-icon>
            </v-btn>
            <v-btn icon @click="handleButtonClick">
                <v-icon>mdi-email</v-icon>
            </v-btn>
            <v-btn icon @click="handleButtonClick">
                <v-icon>mdi-cog</v-icon>
            </v-btn>
        </v-app-bar>

        <!-- Ana Sayfa İçeriği Buraya Gelecek -->
    </v-app>
</template>

<script>
export default {
    name: "Toolbar"
}
</script>

<style scoped>

</style>
