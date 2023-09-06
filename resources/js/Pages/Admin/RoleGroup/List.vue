<template>
    <app-layout>
        <v-snackbar
            v-for="(item,index) in alerts"
            :style="{'margin-top':calcMargin(index)}"
            :key="index"
            v-model="alert"
            :timeout="4000"
            variant="text"
            location="top right"
            multi-line
        >
            <v-alert :title="item.title" :text="item.text" :type="item.type"></v-alert>
        </v-snackbar>
        <v-card>
            <v-card-title>
                Admin Role Group List
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
                        <v-btn
                            variant="outlined"
                            color="teal-darken-1"
                            prepend-icon="mdi-plus"
                            class="me-2"
                            text="Create"
                            :href="route('admin.admin_role_group.create')"
                        >
                        </v-btn>
                        <v-btn
                            variant="outlined"
                            color="orange-darken-1"
                            prepend-icon="mdi-download"
                            text="Download"
                            :href="route('admin.admin_role_group.create')"
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
                    <template v-slot:item.admin_count="{ item }">
                        <v-btn
                            variant="outlined"
                            :text="item.selectable.admin_count.toString()"
                            :disabled="item.selectable.admin_count==0"
                            v-on:click="adminDialog = true; this.adminFilter.roleGroupId=item.selectable.id"
                        >
                        </v-btn>
                    </template>

                    <template v-slot:item.process="{ item }">
                        <template v-if="item.selectable.process">
                            <i class="mdi mdi-spin mdi-loading"></i>
                        </template>
                        <template v-else>
                            <v-btn
                                v-if="item.selectable.permissions.view"
                                icon="mdi-eye-outline"
                                size="small"
                                variant="text"
                                :href="route('admin.admin_role_group.show',item.selectable.id)"
                            >
                            </v-btn>
                            <v-btn
                                v-if="item.selectable.permissions.update"
                                icon="mdi-pencil"
                                size="small"
                                variant="text"
                                :href="route('admin.admin_role_group.edit',item.selectable.id)"
                            >
                            </v-btn>
                            <v-btn
                                v-if="item.selectable.permissions.delete"
                                icon="mdi-delete"
                                size="small"
                                variant="text"
                                v-on:click="deleteDialog=true;deleteData=item.selectable"
                            >
                            </v-btn>
                        </template>
                    </template>
                </v-data-table-server>
            </v-card-item>
        </v-card>
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
            v-model="adminDialog"
            width="auto"
        >
            <v-card>
                <v-card-title>
                    Admin Count
                </v-card-title>
                <v-card-item>
                    <v-row>
                        <v-col md="12">
                            <v-text-field
                                variant="outlined"
                                placeholder="Search"
                                prepend-inner-icon="mdi-magnify"
                                v-model="adminSearch"
                            >
                            </v-text-field>
                        </v-col>
                        <v-col md="12" class="text-end">
                            <v-btn
                                variant="outlined"
                                color="indigo-darken-1"
                                prepend-icon="mdi-filter"
                                text="Filter"
                                class="me-2"
                                v-on:click="this.adminFilter.search=adminSearch"
                            >
                            </v-btn>
                            <v-btn
                                variant="outlined"
                                color="orange-darken-1"
                                prepend-icon="mdi-download"
                                text="Download"
                            >
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-item>
                <v-card-item>
                    <v-data-table-server
                        :headers="this.resource.adminColumns"
                        :items-length="this.adminRecordsTotal"
                        :items="this.adminData"
                        :search="this.adminFilter.search"
                        :loading="adminLoading"
                        :sort-by="[
                        {
                            'key':  this.adminFilter.orderColumn,
                            'order':  this.adminFilter.orderDirection
                        }
                    ]"
                        class="elevation-1"
                        hide-default-footers
                        @update:options="adminLoadItems"
                    >

                    </v-data-table-server>
                </v-card-item>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        color="red-accent-4"
                        v-on:click="adminDialog = false">
                        Close Dialog
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Admin/AppLayout.vue";

export default {
    name: "List",
    components: {
        AppLayout,
    },
    data() {
        return {
            alert: false,
            search: '',
            filter: this.resource.filter,
            data: this.resource.data,
            recordsTotal: 0,
            loading: false,
            adminData: [],
            adminSearch: '',
            adminDialog: false,
            adminFilter: {
                search: '',
                orderColumn: 'id',
                orderDirection: 'desc',
                roleGroupId: '',
            },
            adminRecordsTotal: 0,
            adminLoading: false,
            deleteDialog: false,
            deleteData: {},
            alerts: []
        }
    },
    props: {
        resource: {
            type: Object,
            default: {}
        }
    },
    methods: {
        calcMargin(i) {
            return (i * 100) + 'px'
        },
        deleteItem() {
            this.deleteData.process = true;
            // this.$inertia.delete(route('admin.admin_role_group.destroy',this.deleteData.id))
            axios.delete(route('admin.admin_role_group.destroy', this.deleteData.id)).then(response => {
                this.deleteData.process = false;
                if (response.data.process) {
                    this.data = this.data.filter((value) => {
                        return (value !== this.deleteData)
                    });
                    this.recordsTotal--;
                    this.alerts = [];
                    this.alerts.push({
                        'title': 'Deleted',
                        'text': 'Admin Role Group deleted',
                        'type': 'success'
                    })
                    this.alert = true;
                }
            }).catch((error) => {
                this.deleteData.process = false;

                this.alerts.push({
                    'title': 'Didn\'t Delete',
                    'text': 'Admin Role Group didn\'t delete',
                    'type': 'error'
                })
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
            axios.get(route('admin.admin_role_group.getData'), {
                params: filterDetail
            }).then(response => {
                this.loading = false;
                this.data = response.data.data;
                this.recordsTotal = response.data.recordsTotal;
            });
        },
        adminLoadItems({page, itemsPerPage, sortBy}) {
            this.adminLoading = true
            let filterDetail = {
                'search': this.filter.search,
                'page': page,
                'limit': itemsPerPage,
                'orderColumn': (sortBy[0] != undefined) ? sortBy[0].key : 'id',
                'orderDirection': (sortBy[0] != undefined) ? sortBy[0].order : 'desc'
            }
            axios.get(route('admin.admin.getData'), {
                params: filterDetail
            }).then(response => {
                this.adminLoading = false;
                this.data = response.data.data;
                this.recordsTotal = response.data.recordsTotal;
            }).catch((error) => {
                this.adminLoading = false;
                // this.$page.props.flash.error = error.response.data.message;
            });
        }
    }

}
</script>

<style scoped>

</style>
