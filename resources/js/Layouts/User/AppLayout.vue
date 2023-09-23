<template>
    <v-layout class="rounded rounded-md">
        <v-app-bar clipped-left>
            <v-app-bar-nav-icon
                v-if="deviceType=='mobile'"
                @click.stop="drawer = !drawer"
            ></v-app-bar-nav-icon>
            <template v-slot:append>
                <v-btn icon="mdi-heart"></v-btn>
                <v-btn icon="mdi-magnify"></v-btn>
                <v-btn
                    @click.stop="rightDrawer = !rightDrawer"
                    icon="mdi-dots-vertical">
                </v-btn>
            </template>
        </v-app-bar>
        <v-navigation-drawer
            v-model="rightDrawer"
            location="right"
            temporary
        >
            <v-list-item
                prepend-avatar="https://randomuser.me/api/portraits/men/78.jpg"
                title="John Leider"
            ></v-list-item>

            <v-divider></v-divider>

            <v-list density="compact" nav>
                <Link :href="route('user.logout')" class="nav-item" method="POST" as="nav">
                    <v-list-item
                        prepend-icon="mdi-logout"
                        :title="trans('user.layout.logout')"
                        value="home"
                    >
                    </v-list-item>
                </Link>
                <v-list-item prepend-icon="mdi-forum" title="About" value="about"></v-list-item>
            </v-list>
        </v-navigation-drawer>
        <v-navigation-drawer
            :permanent="deviceType=='desktop'||deviceType=='tablet'"
            :rail="rail"
            @click="rail = false"
            v-model="drawer"
        >
            <v-list>
                <v-list-item
                    prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
                    title="John Leider"
                    nav
                >
                    <template v-slot:append
                              v-if="!rail && (deviceType=='tablet'||deviceType=='desktop')"
                    >
                        <v-btn
                            variant="text"
                            icon="mdi-chevron-left"
                            @click.stop="rail = !rail"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item
                    :href="route('user.home')"
                    :active="route().current('user.home')"
                    title="Home"
                    prepend-icon="mdi-home"
                    value="home"
                ></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-main class="d-flex align-center justify-center">
            <slot></slot>
        </v-main>
    </v-layout>

</template>

<script>
export default {
    name: "AppLayout",
    data() {
        return {
            drawer: true,
            rightDrawer: false,
            deviceType: '',
            rail: false,
        }
    },
    methods: {
        setDeviceType() {
            const platform = navigator.platform.toLowerCase();
            if (/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/.test(platform)) {
                this.deviceType = 'mobile';
            } else if (/mac|win|linux/i.test(platform)) {
                this.deviceType = 'desktop';
            } else if (/tablet|ipad/i.test(platform)) {
                this.deviceType = 'tablet';
            } else {
                this.deviceType = 'unknown';
            }

            // this.deviceType = 'tablet'
        },
    },
    mounted() {
        this.setDeviceType();
    }
}
</script>

<style scoped>

</style>
