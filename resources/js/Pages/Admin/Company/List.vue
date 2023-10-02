<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Company List
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
                                v-on:click="this.filter.search=this.search"
                            >
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="teal-darken-1"
                                prepend-icon="mdi-plus"
                                class="me-2"
                                text="Create"
                                :href="route('admin.company.create')"
                            >
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="orange-darken-1"
                                prepend-icon="mdi-download"
                                text="Download"
                                :loading="downloadLoading"
                                v-on:click="download"
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
                        class="elevation-1"
                        hide-default-footers
                        @update:options="loadItems"
                    >
                        <template v-slot:item.user_count="{ item }">
                                <v-btn
                                    variant="outlined"
                                    :text="item.raw.user_count.toString()"
                                    :disabled="item.raw.user_count==0"
                                    v-on:click="userDialog = true; this.userFilter.companyId=item.raw.id"
                                >
                                </v-btn>
                        </template>
                        <template v-slot:item.manager_count="{ item }">
                            <v-btn
                                variant="outlined"
                                :text="item.raw.manager_count.toString()"
                                :disabled="item.raw.manager_count==0"
                                v-on:click="managerDialog = true; this.managerFilter.companyId=item.raw.id"
                            >
                            </v-btn>
                        </template>
                        <template v-slot:item.is_active="{ item }">
                            <template v-if=" item.raw.is_active">
                                Active
                            </template>
                            <template v-else>
                                Passive
                            </template>
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
                                    :href="route('admin.company.show',item.raw.id)"
                                >
                                </v-btn>
                                <v-btn
                                    v-if="item.raw.permissions.update"
                                    icon="mdi-pencil"
                                    size="small"
                                    variant="text"
                                    :href="route('admin.company.edit',item.raw.id)"
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
                                <v-btn
                                    icon="mdi-account-group"
                                    size="small"
                                    variant="text"
                                    target="_blank"
                                    :href="route('admin.main_user.login',item.raw.id)"
                                >
                                    <v-icon>
                                        mdi-account-group
                                    </v-icon>
                                    <v-tooltip activator="parent">
                                        Login Main User
                                    </v-tooltip>
                                </v-btn>
                                <v-btn
                                    icon="mdi-account-supervisor"
                                    size="small"
                                    variant="text"
                                    target="_blank"
                                    :href="route('admin.supervisor.login',item.raw.id)"
                                >
                                    <v-icon>
                                        mdi-account-supervisor
                                    </v-icon>
                                    <v-tooltip activator="parent">
                                        Login Supervisor
                                    </v-tooltip>
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
        <v-dialog
            transition="dialog-bottom-transition"
            v-model="managerDialog"
            width="auto"
        >
            <v-card>
                <v-card-title>
                    Manager Count
                </v-card-title>
                <v-card-item>
                    <v-row>
                        <v-col md="12">
                            <v-text-field
                                variant="outlined"
                                placeholder="Search"
                                prepend-inner-icon="mdi-magnify"
                                v-model="managerSearch"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col md="12" class="text-end">
                            <v-btn
                                variant="outlined"
                                color="indigo-darken-1"
                                prepend-icon="mdi-filter"
                                class="me-2"
                                v-on:click="this.managerFilter.search=managerSearch"
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
                        :headers="this.resource.managerColumns"
                        :items-length="this.managerRecordsTotal"
                        :items="this.managerData"
                        :search="this.managerFilter.search"
                        :loading="managerLoading"
                        :sort-by="[
                        {
                            'key':  this.managerFilter.orderColumn,
                            'order':  this.managerFilter.orderDirection
                        }
                    ]"
                        class="elevation-1"
                        hide-default-footers
                        @update:options="managerLoadItems"
                    >

                    </v-data-table-server>
                </v-card-item>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        color="red-accent-4"
                        v-on:click="managerDialog = false">
                        Close Dialog
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </AdminAppLayout>
</template>

<script>
export default {
    name: "List",
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
            downloadLoading: false,
            userData: [],
            userSearch: '',
            userDialog: false,
            userFilter: {
                search: '',
                orderColumn: 'id',
                orderDirection: 'desc',
                companyId: '',
            },
            userRecordsTotal: 0,
            userLoading: false,
            managerData: [],
            managerSearch: '',
            managerDialog: false,
            managerFilter: {
                search: '',
                orderColumn: 'id',
                orderDirection: 'desc',
                companyId: '',
            },
            managerRecordsTotal: 0,
            managerLoading: false,
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
            axios.get(route('admin.company.getData'), {
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
            axios.delete(route('admin.company.destroy', this.deleteData.id)).then(response => {
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
            this.downloadLoading = true
            let filterDetail = {
                'search': this.filter.search,
            }
            axios.post(route('admin.company.download'), {
                params: filterDetail
            }).then(response => {
                this.downloadLoading = false;
                this.$page.props.alert = [{
                    'title': response.data.message,
                    'text': '',
                    'type': (response.data.process) ? 'success' : 'warning'
                }];
            });
        },
        userLoadItems({page, itemsPerPage, sortBy}) {
            this.userLoading = true
            let filterDetail = {
                'companyId': this.userFilter.companyId,
                'search': this.userFilter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('admin.user.getData'), {
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
        managerLoadItems({page, itemsPerPage, sortBy}) {
            this.managerLoading = true
            let filterDetail = {
                'companyId': this.managerFilter.companyId,
                'search': this.managerFilter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('admin.manager.getData'), {
                params: filterDetail
            }).then(response => {
                this.managerLoading = false;
                this.managerData = response.data.data;
                this.managerRecordsTotal = response.data.recordsTotal;
            }).catch((error) => {
                this.managerLoading = false;
                // this.$page.props.flash.error = error.response.data.message;
            });
        },
    },
    mounted() {
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: false,
                href: route('admin.home'),
            },
            {
                title: 'Company List',
                disabled: true,
                href: route('admin.company.index'),
            },
        ]
    }
}
</script>

<style scoped>

</style>
