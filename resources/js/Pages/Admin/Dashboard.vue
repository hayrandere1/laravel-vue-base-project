<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            Admin Dashboard
        </v-container>
    </AdminAppLayout>
</template>

<script>

export default {
    name: "Dashboard",
    mounted() {
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: true,
                href: route('admin.home'),
            },
        ]
    }

}
</script>

<style scoped>

</style>
