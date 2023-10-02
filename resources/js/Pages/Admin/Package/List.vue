<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Package List
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
                                :href="route('admin.package.create')"
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
                        <template v-slot:item.is_active="{ item }">
                            <template v-if=" item.raw.is_active">
                                Active
                            </template>
                            <template v-else>
                                Passive
                            </template>
                        </template>
                        <template v-slot:item.company_count="{ item }">
                            <v-btn
                                variant="outlined"
                                :text="item.raw.company_count.toString()"
                                :disabled="item.raw.company_count==0"
                                v-on:click="companyDialog = true; this.companyFilter.packageId=item.raw.id"
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
                                    :href="route('admin.package.show',item.raw.id)"
                                >
                                </v-btn>
                                <v-btn
                                    v-if="item.raw.permissions.update"
                                    icon="mdi-pencil"
                                    size="small"
                                    variant="text"
                                    :href="route('admin.package.edit',item.raw.id)"
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
            v-model="companyDialog"
            width="auto"
        >
            <v-card>
                <v-card-title>
                    Company Count
                </v-card-title>
                <v-card-item>
                    <v-row>
                        <v-col md="12">
                            <v-text-field
                                variant="outlined"
                                placeholder="Search"
                                prepend-inner-icon="mdi-magnify"
                                v-model="companySearch"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col md="12" class="text-end">
                            <v-btn
                                variant="outlined"
                                color="indigo-darken-1"
                                prepend-icon="mdi-filter"
                                class="me-2"
                                v-on:click="this.companyFilter.search=companySearch"
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
                        :headers="this.resource.companyColumns"
                        :items-length="this.companyRecordsTotal"
                        :items="this.companyData"
                        :search="this.companyFilter.search"
                        :loading="companyLoading"
                        :sort-by="[
                        {
                            'key':  this.companyFilter.orderColumn,
                            'order':  this.companyFilter.orderDirection
                        }
                    ]"
                        class="elevation-1"
                        hide-default-footers
                        @update:options="companyLoadItems"
                    >

                    </v-data-table-server>
                </v-card-item>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        color="red-accent-4"
                        v-on:click="companyDialog = false">
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
            downloadLoading:false,
            companyData: [],
            companySearch: '',
            companyDialog: false,
            companyFilter: {
                search: '',
                orderColumn: 'id',
                orderDirection: 'desc',
                packageId: '',
            },
            companyRecordsTotal: 0,
            companyLoading: false,
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
            axios.get(route('admin.package.getData'), {
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
            axios.delete(route('admin.package.destroy', this.deleteData.id)).then(response => {
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
        companyLoadItems({page, itemsPerPage, sortBy}) {
            this.companyLoading = true
            let filterDetail = {
                'packageId': this.companyFilter.packageId,
                'search': this.companyFilter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('admin.company.getData'), {
                params: filterDetail
            }).then(response => {
                this.companyLoading = false;
                this.companyData = response.data.data;
                this.companyRecordsTotal = response.data.recordsTotal;
            }).catch((error) => {
                this.companyLoading = false;
                // this.$page.props.flash.error = error.response.data.message;
            });
        },
        download() {
            this.downloadLoading = true
            let filterDetail = {
                'search': this.filter.search,
            }
            axios.post(route('admin.package.download'), {
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
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: false,
                href: route('admin.home'),
            },
            {
                title: 'Package List',
                disabled: true,
                href: route('admin.package.index'),
            },
        ]
    }
}
</script>

<style scoped>

</style>
