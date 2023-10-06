<template>
    <ManagerAppLayout>
        <v-container :fluid="true">
            Manager Dasboard
        </v-container>
    </ManagerAppLayout>
</template>

<script>

export default {
    name: "Dashboard",
    mounted() {
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: true,
                href: route('manager.home'),
            },
        ]
    }

}
</script>

<style scoped>

</style>
