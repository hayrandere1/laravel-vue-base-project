<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            Admin Dashboard
            <v-data-table
                items-per-page="10"
                :headers="[
                 {
                    title : 'User Type',
                    align: 'start',
                    key : 'user_type',
                },
                 {
                    title : 'User ID',
                    align: 'start',
                    key : 'user_id',
                },
                 {
                    title : 'Ip Address',
                    align: 'start',
                    key : 'ip_address',
                },
                 {
                    title : 'Created At',
                    align: 'start',
                    key : 'created_at',
                },
                 {
                    title : 'Updated At',
                    align: 'start',
                    key : 'updated_at',
                },
                    ]"
                :items="this.$page.props.sessions"
                item-value="name"
                class="elevation-1"
            >
            </v-data-table>
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
