<template>
    <UserAppLayout>
        <v-container :fluid="true">
            User Dashboard
        </v-container>
    </UserAppLayout>
</template>

<script>

export default {
    name: "Dashboard",
    mounted() {
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: true,
                href: route('user.home'),
            },
        ]
    }

}
</script>

<style scoped>

</style>
