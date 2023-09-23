<template>
    <ManagerAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Manager Role Group List
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
                                :href="route('manager.manager_role_group.create')"
                            >
                                Create
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="orange-darken-1"
                                prepend-icon="mdi-download"
                                :href="route('manager.manager_role_group.create')"
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
                        <template v-slot:item.manager_count="{ item }">
                            <v-btn
                                variant="outlined"
                                :text="item.raw.manager_count.toString()"
                                :disabled="item.raw.manager_count==0"
                                v-on:click="managerDialog = true; this.managerFilter.roleGroupId=item.raw.id"
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
                                    :href="route('manager.manager_role_group.show',item.raw.id)"
                                >
                                </v-btn>
                                <v-btn
                                    v-if="item.raw.permissions.update"
                                    icon="mdi-pencil"
                                    size="small"
                                    variant="text"
                                    :href="route('manager.manager_role_group.edit',item.raw.id)"
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
            managerData: [],
            managerSearch: '',
            managerDialog: false,
            managerFilter: {
                search: '',
                orderColumn: 'id',
                orderDirection: 'desc',
                roleGroupId: '',
            },
            managerRecordsTotal: 0,
            managerLoading: false,
            deleteDialog: false,
            deleteData: {},
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
            // this.$inertia.delete(route('manager.manager_role_group.destroy',this.deleteData.id))
            axios.delete(route('manager.manager_role_group.destroy', this.deleteData.id)).then(response => {
                this.deleteData.process = false;
                if (response.data.process) {
                    this.data = this.data.filter((value) => {
                        return (value !== this.deleteData)
                    });
                    this.recordsTotal--;
                    this.$page.props.alert = [{
                        'title': 'Deleted',
                        'text': 'Manager Role Group deleted',
                        'type': 'success'
                    }];
                }
            }).catch((error) => {
                this.deleteData.process = false;

                this.$page.props.alert = [{
                    'title': 'Didn\'t Delete',
                    'text': 'Manager Role Group didn\'t delete',
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
            axios.get(route('manager.manager_role_group.getData'), {
                params: filterDetail
            }).then(response => {
                this.loading = false;
                this.data = response.data.data;
                this.recordsTotal = response.data.recordsTotal;
            });
        },
        managerLoadItems({page, itemsPerPage, sortBy}) {
            this.managerLoading = true
            let filterDetail = {
                'search': this.filter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('manager.manager.getData'), {
                params: filterDetail
            }).then(response => {
                this.managerLoading = false;
                this.managerData = response.data.data;
                this.managerRecordsTotal = response.data.recordsTotal;
            }).catch((error) => {
                this.managerLoading = false;
                // this.$page.props.flash.error = error.response.data.message;
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
