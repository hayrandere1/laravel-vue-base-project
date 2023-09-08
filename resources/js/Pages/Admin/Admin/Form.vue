<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Admin Form
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
                        <v-autocomplete
                            v-model="form.role_group_id"
                            label="Admin Role Group"
                            :items="adminRoleGroups"
                            variant="outlined"
                            item-title="name"
                            item-value="id"
                        ></v-autocomplete>


                        <v-text-field
                            variant="outlined"
                            v-model="form.username"
                            label="Username"
                            required
                            :error-messages="form.errors.username"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.first_name"
                            label="First Name"
                            required
                            :error-messages="form.errors.first_name"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.last_name"
                            label="Last Name"
                            required
                            :error-messages="form.errors.last_name"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.email"
                            label="Email"
                            type="email"
                            required
                            :error-messages="form.errors.email"
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
            form: useForm({
                username: (this.admin.username) ? this.admin.username : '',
                role_group_id: (this.admin.role_group_id) ? this.admin.role_group_id : '',
                first_name: (this.admin.first_name) ? this.admin.first_name : '',
                last_name: (this.admin.last_name) ? this.admin.last_name : '',
                email: (this.admin.email) ? this.admin.email : '',
                is_active: (this.admin.is_active) ? this.admin.is_active : 0,
            }),
        }
    },
    props: {
        admin: {
            type: Object,
            default: {}
        },
        adminRoleGroups: {
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
            return typeof (this.admin.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.admin.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        save() {
            if (this.isUpdate()) {
                this.form.put(route('admin.admin.update', this.admin.id));
            } else if (this.isCreate()) {
                this.form.post(route('admin.admin.store'));
            }
        },
    }
}
</script>

<style scoped>

</style>
