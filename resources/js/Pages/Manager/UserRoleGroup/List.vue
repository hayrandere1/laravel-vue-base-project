<template>
    <ManagerAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    User Role Group List
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
                                class="float-left"
                                v-on:click="this.filter.search=search"
                            >
                                Filter
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="teal-darken-1"
                                prepend-icon="mdi-plus"
                                class="me-2"
                                :href="route('manager.user_role_group.create')"
                            >
                                Create
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="orange-darken-1"
                                prepend-icon="mdi-download"
                                :loading="downloadLoading"
                                v-on:click="download"
                            >
                                Download
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
                        <template v-slot:item.user_count="{ item }">
                            <v-btn
                                variant="outlined"
                                :text="item.raw.user_count.toString()"
                                :disabled="item.raw.user_count==0"
                                v-on:click="userDialog = true; this.userFilter.roleGroupId=item.raw.id"
                            >
                            </v-btn>
                        </template>

                        <template v-slot:item.process="{ item }">
                            <template v-if="item.raw.process">
                                <i class="mdi mdi-spin mdi-loading"></i>
                            </template>
                            <template v-else>
                                <v-btn
                                    v-if="item.raw.permissions.view"
                                    icon="mdi-eye-outline"
                                    size="small"
                                    variant="text"
                                    :href="route('manager.user_role_group.show',item.raw.id)"
                                >
                                </v-btn>
                                <v-btn
                                    v-if="item.raw.permissions.update"
                                    icon="mdi-pencil"
                                    size="small"
                                    variant="text"
                                    :href="route('manager.user_role_group.edit',item.raw.id)"
                                >
                                </v-btn>
                                <v-btn
                                    v-if="item.raw.permissions.delete"
                                    icon="mdi-delete"
                                    size="small"
                                    variant="text"
                                    v-on:click="deleteDialog=true;deleteData=item.raw"
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
                        class="me-2"
                        v-on:click="deleteDialog=false"
                    >
                        No
                    </v-btn>
                    <v-btn
                        variant="outlined"
                        color="red-accent-4"
                        v-on:click="deleteDialog=false;deleteItem();"
                    >
                        Yes
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-dialog
            transition="dialog-bottom-transition"
            v-model="userDialog"
            width="auto"
        >
            <v-card>
                <v-card-title>
                    User Count
                </v-card-title>
                <v-card-item>
                    <v-row>
                        <v-col md="12">
                            <v-text-field
                                variant="outlined"
                                placeholder="Search"
                                prepend-inner-icon="mdi-magnify"
                                v-model="userSearch"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col md="12" class="text-end">
                            <v-btn
                                variant="outlined"
                                color="indigo-darken-1"
                                prepend-icon="mdi-filter"
                                class="me-2"
                                v-on:click="this.userFilter.search=userSearch"
                            >
                                Filter
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="orange-darken-1"
                                prepend-icon="mdi-download"
                            >
                                Download
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-item>
                <v-card-item>
                    <v-data-table-server
                        :headers="this.resource.userColumns"
                        :items-length="this.userRecordsTotal"
                        :items="this.userData"
                        :search="this.userFilter.search"
                        :loading="userLoading"
                        :sort-by="[
                        {
                            'key':  this.userFilter.orderColumn,
                            'order':  this.userFilter.orderDirection
                        }
                    ]"
                        class="elevation-1"
                        hide-default-footers
                        @update:options="userLoadItems"
                    >

                    </v-data-table-server>
                </v-card-item>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        color="red-accent-4"
                        v-on:click="userDialog = false">
                        Close Dialog
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </ManagerAppLayout>
</template>

<script>

export default {
    name: "List",
    components: {},
    data() {
        return {
            search: '',
            filter: this.resource.filter,
            data: this.resource.data,
            recordsTotal: 0,
            loading: false,
            userData: [],
            userSearch: '',
            userDialog: false,
            userFilter: {
                search: '',
                orderColumn: 'id',
                orderDirection: 'desc',
                roleGroupId: '',
            },
            userRecordsTotal: 0,
            userLoading: false,
            deleteDialog: false,
            deleteData: {},
            downloadLoading:false,
        }
    },
    props: {
        resource: {
            type: Object,
            default: {}
        }
    },
    methods: {
        deleteItem() {
            this.deleteData.process = true;
            // this.$inertia.delete(route('manager.user_role_group.destroy',this.deleteData.id))
            axios.delete(route('manager.user_role_group.destroy', this.deleteData.id)).then(response => {
                this.deleteData.process = false;
                if (response.data.process) {
                    this.data = this.data.filter((value) => {
                        return (value !== this.deleteData)
                    });
                    this.recordsTotal--;
                    this.$page.props.alert = [{
                        'title': 'Deleted',
                        'text': 'User Role Group deleted',
                        'type': 'success'
                    }];
                }
            }).catch((error) => {
                this.deleteData.process = false;

                this.$page.props.alert = [{
                    'title': 'Didn\'t Delete',
                    'text': 'User Role Group didn\'t delete',
                    'type': 'error'
                }];
                // this.$page.props.flash.error = error.response.data.message;
            });
        },
        loadItems({page, itemsPerPage, sortBy}) {
            this.loading = true
            let filterDetail = {
                'search': this.filter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('manager.user_role_group.getData'), {
                params: filterDetail
            }).then(response => {
                this.loading = false;
                this.data = response.data.data;
                this.recordsTotal = response.data.recordsTotal;
            });
        },
        userLoadItems({page, itemsPerPage, sortBy}) {
            this.userLoading = true
            let filterDetail = {
                'search': this.filter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('manager.user.getData'), {
                params: filterDetail
            }).then(response => {
                this.userLoading = false;
                this.userData = response.data.data;
                this.userRecordsTotal = response.data.recordsTotal;
            }).catch((error) => {
                this.userLoading = false;
                // this.$page.props.flash.error = error.response.data.message;
            });
        },
        download() {
            this.downloadLoading = true
            let filterDetail = {
                'search': this.filter.search,
            }
            axios.post(route('manager.user_role_group.download'), {
                params: filterDetail
            }).then(response => {
                this.downloadLoading = false;
                this.$page.props.alert = [{
                    'title': response.data.message,
                    'text': '',
                    'type': (response.data.process) ? 'success' : 'warning'
                }];
            });
        }
    },
    mounted() {
        this.$page.props.breadcrumbs =[
            {
                title: 'Dashboard',
                disabled: false,
                href: route('manager.home'),
            },
            {
                title: 'Role Group List',
                disabled: true,
                href: route('manager.home'),
            },
        ]
    }
}
</script>

<style scoped>

</style>
