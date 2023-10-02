<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Company Form
                </v-card-title>
                <v-card-item>
                    <v-form v-on:submit.prevent="save" :disabled="isShow()">
                        <v-switch
                            class="ms-3"
                            v-model="form.is_active"
                            label="Active"
                            :true-value="1"
                            :false-value="0"
                            color="success"
                        >
                        </v-switch>
                        <v-text-field
                            variant="outlined"
                            v-model="form.name"
                            label="Name"
                            required
                            :error-messages="form.errors.name"
                        >
                        </v-text-field>
                        <v-autocomplete
                            v-model="form.package_id"
                            label="Package"
                            :items="packages"
                            variant="outlined"
                            item-title="name"
                            item-value="id"
                        ></v-autocomplete>
                        <v-menu
                            v-model="dueDateMenu"
                            :close-on-content-click="false">
                            <template v-slot:activator="{ props }">
                                <v-text-field
                                    variant="outlined"
                                    prepend-inner-icon="mdi-calendar"
                                    v-bind="props"
                                    label="Due Date"
                                    v-model="this.form.due_date"
                                >
                                </v-text-field>
                            </template>
                            <v-date-picker
                                v-model="this.form.due_date"
                                show-adjacent-months
                                input-mode="calendar"
                                calendar-icon=""
                                header=""
                                title=""
                                collapse-icon=""
                                keyboard-icon=""
                                v-on:click:save="dueDateMenu=false"
                                v-on:click:cancel="dueDateMenu=false"
                            >
                            </v-date-picker>
                        </v-menu>
                        <v-card-title>
                            Supervisor
                        </v-card-title>
                        <v-divider :thickness="1" class="border-opacity-100 mb-3"></v-divider>
                        <v-text-field
                            variant="outlined"
                            v-model="form.supervisor.username"
                            label="Username"
                            required
                            :error-messages="(form.errors.supervisor!=undefined)?form.errors.supervisor.username:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.supervisor.first_name"
                            label="First Name"
                            required
                            :error-messages="(form.errors.supervisor!=undefined)?form.errors.supervisor.first_name:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.supervisor.last_name"
                            label="Last Name"
                            required
                            :error-messages="(form.errors.supervisor!=undefined)?form.errors.supervisor.last_name:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.supervisor.email"
                            label="Email"
                            required
                            :error-messages="(form.errors.supervisor!=undefined)?form.errors.supervisor.email:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.supervisor.phone"
                            label="Phone"
                            required
                            :error-messages="(form.errors.supervisor!=undefined)?form.errors.supervisor.phone:''"
                        >
                        </v-text-field>
                        <v-card-title>
                            Main User
                        </v-card-title>
                        <v-divider :thickness="1" class="border-opacity-100 mb-3"></v-divider>
                        <v-text-field
                            variant="outlined"
                            v-model="form.mainUser.username"
                            label="Username"
                            required
                            :error-messages="(form.errors.mainUser!=undefined)?form.errors.mainUser.username:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.mainUser.first_name"
                            label="First Name"
                            required
                            :error-messages="(form.errors.mainUser!=undefined)?form.errors.mainUser.first_name:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.mainUser.last_name"
                            label="Last Name"
                            required
                            :error-messages="(form.errors.mainUser!=undefined)?form.errors.mainUser.last_name:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.mainUser.email"
                            label="Email"
                            required
                            :error-messages="(form.errors.mainUser!=undefined)?form.errors.mainUser.email:''"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.mainUser.phone"
                            label="Phone"
                            required
                            :error-messages="(form.errors.mainUser!=undefined)?form.errors.mainUser.phone:''"
                        >
                        </v-text-field>
                        <v-btn
                            v-if="!isShow()"
                            type="submit"
                            color="primary"
                            class=" float-end"
                            :disabled="form.processing"
                            :loading="form.processing"
                        >
                            Save
                        </v-btn>
                    </v-form>
                </v-card-item>
            </v-card>
        </v-container>
    </AdminAppLayout>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "Form",
    data() {
        return {
            dueDateMenu: false,
            form: useForm({
                name: (this.company.name) ? this.company.name : '',
                package_id: (this.company.package_id) ? this.company.package_id : '',
                due_date: (this.company.due_date) ? this.company.due_date : '',
                is_active: (this.company.is_active) ? this.company.is_active : 1,
                supervisor: (this.company.supervisor) ? this.company.supervisor : {},
                mainUser: (this.company.mainUser) ? this.company.mainUser : {},
            }),
        }
    },
    props: {
        company: {
            type: Object,
            default: {}
        },
        packages: {
            type: Object,
            default: {}
        },
        show: {
            type: Boolean,
            default: false
        },
    },
    methods: {
        isCreate() {
            return typeof (this.company.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.company.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        save() {
            if (this.isUpdate()) {
                this.form.put(route('admin.company.update', this.company.id));
            } else if (this.isCreate()) {
                this.form.post(route('admin.company.store'));
            }
        },
    }, mounted() {
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: false,
                href: route('admin.home'),
            },
            {
                title: 'Company List',
                disabled: false,
                href: route('admin.company.index'),
            },
            {
                title: 'Company Form',
                disabled: true,
            },
        ]
    }
}
</script>

<style scoped>

</style>
