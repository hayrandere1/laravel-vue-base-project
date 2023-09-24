<template>
    <UserAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    User Form
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
                            label="User Role Group"
                            :items="userRoleGroups"
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
    </UserAppLayout>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "Form",
    data() {
        return {
            form: useForm({
                username: (this.user.username) ? this.user.username : '',
                role_group_id: (this.user.role_group_id) ? this.user.role_group_id : '',
                first_name: (this.user.first_name) ? this.user.first_name : '',
                last_name: (this.user.last_name) ? this.user.last_name : '',
                email: (this.user.email) ? this.user.email : '',
                is_active: (this.user.is_active) ? this.user.is_active : 1,
            }),
        }
    },
    props: {
        user: {
            type: Object,
            default: {}
        },
        userRoleGroups: {
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
            return typeof (this.user.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.user.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        save() {
            if (this.isUpdate()) {
                this.form.put(route('user.user.update', this.user.id));
            } else if (this.isCreate()) {
                this.form.post(route('user.user.store'));
            }
        },
    },mounted() {
        this.$page.props.breadcrumbs =[
            {
                title: 'Dashboard',
                disabled: false,
                href: route('user.home'),
            },
            {
                title: 'User List',
                disabled: false,
                href: route('user.user.index'),
            },
            {
                title: 'User Form',
                disabled: true,
            },
        ]
    }
}
</script>

<style scoped>

</style>
