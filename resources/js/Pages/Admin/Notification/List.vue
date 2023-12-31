<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Notification List
                </v-card-title>
                <v-card-item>
                    <v-row>
                        <v-col md="12">
                            <v-text-field
                                variant="outlined"
                                placeholder="Search"
                                prepend-inner-icon="mdi-magnify"
                                v-model="search"
                                hide-details
                            >
                            </v-text-field>
                        </v-col>
                        <v-col md="12" class="text-end">
                            <v-btn
                                variant="outlined"
                                color="indigo-darken-1"
                                prepend-icon="mdi-filter"
                                text="Filter"
                                class="float-left"
                                v-on:click="this.filter.search=search"
                            >
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-item>
                <v-card-item>
                    <v-data-table-server
                        v-model:items-per-page="this.filter.limit"
                        :headers="this.resource.columns"
                        :items-length="this.recordsTotal"
                        :items="this.data"
                        :search="this.filter.search"
                        :loading="loading"
                        :sort-by="[
                        {
                            'key':  this.filter.orderColumn,
                            'order':  this.filter.orderDirection
                        }
                    ]"
                        class="elevation-1"
                        hide-default-footers
                        @update:options="loadItems"
                    >
                        <template v-slot:item.process="{ item }">
                            <template v-if="item.selectable.process">
                                <i class="mdi mdi-spin mdi-loading"></i>
                            </template>
                            <template v-else>
                                <v-btn
                                    v-if="item.selectable.permissions.delete"
                                    icon="mdi-delete"
                                    size="small"
                                    variant="text"
                                    v-on:click="deleteDialog=true;deleteData=item.selectable"
                                >
                                </v-btn>
                                <v-btn
                                    v-if="item.selectable.permissions.view"
                                    icon="mdi-link"
                                    size="small"
                                    variant="text"
                                    v-on:click="item.is_read=1;"
                                    :href="route('admin.notification.show',item.id)"
                                >
                                </v-btn>
                            </template>
                        </template>
                    </v-data-table-server>
                </v-card-item>
            </v-card>
        </v-container>

        <v-dialog
            transition="dialog-bottom-transition"
            v-model="deleteDialog"
            width="auto"
        >
            <v-card>
                <v-card-title>
                    Are you sure you want to delete?
                </v-card-title>
                <v-card-text class="text-end">
                    <v-btn
                        variant="outlined"
                        color="gray-accent-4"
                        text="No"
                        class="me-2"
                        v-on:click="deleteDialog=false"
                    >
                    </v-btn>
                    <v-btn
                        variant="outlined"
                        color="red-accent-4"
                        text="Yes"
                        v-on:click="deleteDialog=false;deleteItem();"
                    >
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-dialog>
    </AdminAppLayout>
</template>

<script>

export default {
    name: "List",
    components: {},
    props: {
        resource: {
            type: Object,
            default: {}
        }
    },
    data() {
        return {
            search: '',
            filter: this.resource.filter,
            data: this.resource.data,
            recordsTotal: 0,
            loading: false,
            deleteDialog: false,
            deleteData: {},
        }
    },
    methods: {
        loadItems({page, itemsPerPage, sortBy}) {
            this.loading = true
            let filterDetail = {
                'search': this.filter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('admin.notification.getData'), {
                params: filterDetail
            }).then(response => {
                this.loading = false;
                this.data = response.data.data;
                this.recordsTotal = response.data.recordsTotal;
            });
        },
        deleteItem() {
            this.deleteData.process = true;
            // this.$inertia.delete(route('admin.admin_role_group.destroy',this.deleteData.id))
            axios.delete(route('admin.notification.destroy', this.deleteData.id)).then(response => {
                this.deleteData.process = false;
                if (response.data.process) {
                    this.data = this.data.filter((value) => {
                        return (value !== this.deleteData)
                    });
                    this.recordsTotal--;
                }
            }).catch((error) => {
                this.deleteData.process = false;
                // this.$page.props.flash.error = error.response.data.message;
            });
        },
        download() {
            // this.$inertia.post(route('admin.notification.download'), this.resource.filter, {preserveState: true})
            // this.loading = true
            let filterDetail = {
                'search': this.filter.search,
            }
            axios.post(route('admin.notification.download'), {
                params: filterDetail
            }).then(response => {
                // this.loading = false;
                console.log(4, response.data)
                // this.data = response.data.data;
                // this.recordsTotal = response.data.recordsTotal;
            });
        },
        rowDownload(item) {
            console.log(5, item)
            axios.get(route('admin.notification.download', item.id), {responseType: 'blob'})
                .then(response => {
                    const blob = new Blob([response.data], {type: 'application/csv'});
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = response.headers['content-disposition'].split('filename=')[1];
                    link.click();
                    URL.revokeObjectURL(link.href);
                }).catch(console.error);
        }
    },
    mounted() {
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: false,
                href: route('admin.home'),
            },
            {
                title: 'Notification List',
                disabled: true,
                href: route('admin.notification.index'),
            },
        ]
    }
}
</script>

<style scoped>

</style>
