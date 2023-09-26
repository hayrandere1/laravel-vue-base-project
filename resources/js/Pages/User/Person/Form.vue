<template>
    <UserAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Group Form
                </v-card-title>
                <v-card-item>
                    <v-form v-on:submit.prevent="save" :disabled="isShow()"  class="mt-3">
                        <v-text-field
                            variant="outlined"
                            v-model="form.name"
                            label="Name"
                            required
                            :error-messages="form.errors.name"
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
                name: (this.group.name) ? this.group.name : '',
            }),
        }
    },
    props: {
        group: {
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
            return typeof (this.group.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.group.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        save() {
            if (this.isUpdate()) {
                this.form.put(route('user.group.update', this.group.id));
            } else if (this.isCreate()) {
                this.form.post(route('user.group.store'));
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
                title: 'Group List',
                disabled: false,
                href: route('user.group.index'),
            },
            {
                title: 'Group Form',
                disabled: true,
            },
        ]
    }
}
</script>

<style scoped>

</style>
