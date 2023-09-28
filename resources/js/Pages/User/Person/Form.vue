<template>
    <UserAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Person Form
                </v-card-title>
                <v-card-item>
                    <v-form v-on:submit.prevent="save" :disabled="isShow()"  class="mt-3">
                        <v-autocomplete
                            v-model="form.group_id"
                            label="Group"
                            :items="groups"
                            variant="outlined"
                            item-title="name"
                            item-value="id"
                        ></v-autocomplete>
                        <v-text-field
                            variant="outlined"
                            v-model="form.name"
                            label="Name"
                            required
                            :error-messages="form.errors.name"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.email"
                            label="Email"
                            required
                            :error-messages="form.errors.email"
                        >
                        </v-text-field>
                        <v-text-field
                            variant="outlined"
                            v-model="form.phone"
                            label="Phone"
                            required
                            :error-messages="form.errors.phone"
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
                name: (this.person.name) ? this.person.name : '',
                group_id: (this.person.group_id) ? this.person.group_id : '',
                email: (this.person.email) ? this.person.email : '',
                phone: (this.person.phone) ? this.person.phone : '',
            }),
        }
    },
    props: {
        person: {
            type: Object,
            default: {}
        },
        groups: {
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
            return typeof (this.person.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.person.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        save() {
            if (this.isUpdate()) {
                this.form.put(route('user.person.update', this.person.id));
            } else if (this.isCreate()) {
                this.form.post(route('user.person.store'));
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
                title: 'Person List',
                disabled: false,
                href: route('user.person.index'),
            },
            {
                title: 'Person Form',
                disabled: true,
            },
        ]
    }
}
</script>

<style scoped>

</style>
