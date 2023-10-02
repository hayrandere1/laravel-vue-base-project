<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Package Form
                </v-card-title>
                <v-card-item>
                    <v-form v-on:submit.prevent="save" :disabled="isShow()" class="mt-3">
                        <v-text-field
                            variant="outlined"
                            v-model="form.name"
                            label="Name"
                            required
                            :error-messages="form.errors.name"
                        >
                        </v-text-field>
                        <p class="text-h4 mt-3">
                            Manager Roles
                        </p>
                        <v-divider class="border-opacity-100"></v-divider>
                        <v-row>
                            <template v-for="(role,key) in this.form.managerRoles">
                                <v-col cols="12" class="mb-3">
                                    <v-card>
                                        <template v-slot:title>
                                            {{ role['route_name'] }}
                                        </template>

                                        <template v-slot:subtitle>

                                            <v-switch
                                                v-model="allManagerSwitches[key]"
                                                v-on:change="allManagerSwitchChange(key)"
                                                color="success"
                                                class="me-2"
                                                label="Hepsini Aç"
                                                inset
                                            ></v-switch>
                                        </template>

                                        <template v-slot:text>
                                            <v-table :eager="true">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Role</th>
                                                    <th>Detail</th>
                                                    <th>Extra</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="childRole in role['children']">
                                                    <td>
                                                        <v-switch
                                                            color="primary"
                                                            hide-details
                                                            v-model="childRole.checked"
                                                            inset
                                                        ></v-switch>
                                                    </td>
                                                    <td>
                                                        {{ childRole.route_name }}
                                                    </td>
                                                    <td>
                                                        <template v-if="childRole.model">
                                                            <template v-for="model in JSON.parse(childRole.model)">
                                                                <v-select
                                                                    v-model="childRole.permissionTypes[model]"
                                                                    :label="'Select '+model"
                                                                    item-title="title"
                                                                    item-value="value"
                                                                    :items="this.permissionTypesValues['webUser']"
                                                                ></v-select>
                                                            </template>
                                                        </template>
                                                        <template v-else>-</template>
                                                    </td>
                                                    <td>
                                                        <template v-if="childRole.model">
                                                            <template v-for="model in JSON.parse(childRole.model)">
                                                                <v-select
                                                                    v-if="['only_selected_values','except_selected_values'].includes(childRole.permissionTypes[model])"
                                                                    v-model="childRole['permissionValues'][model]"
                                                                    :items="this.values[model]"
                                                                    item-title="number"
                                                                    item-value="id"
                                                                    :label="'Select '+model+' item'"
                                                                    multiple
                                                                >
                                                                    <template v-slot:selection="{ item, index }">
                                                                        <v-chip v-if="index < 1">
                                                                            <span>{{ item.title }}</span>
                                                                        </v-chip>
                                                                        <span
                                                                            v-if="index === 1"
                                                                            class="text-grey text-caption align-self-center"
                                                                        >
                                                                           (+{{
                                                                                childRole.permissionValues[model].length - 1
                                                                            }} diğer)
                                                                    </span>
                                                                    </template>
                                                                </v-select>
                                                            </template>
                                                        </template>
                                                        <template v-else>-</template>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </v-table>
                                        </template>
                                    </v-card>
                                </v-col>
                            </template>
                        </v-row>
                        <p class="text-h4 mt-3">
                            User Roles
                        </p>
                        <v-divider class="border-opacity-100"></v-divider>
                        <v-row>
                            <template v-for="(role,key) in this.form.userRoles">
                                <v-col cols="12" class="mb-3">
                                    <v-card>
                                        <template v-slot:title>
                                            {{ role['route_name'] }}
                                        </template>

                                        <template v-slot:subtitle>
                                            <v-switch
                                                v-model="allUserSwitches[key]"
                                                v-on:change="allUserSwitchChange(key)"
                                                color="success"
                                                class="me-2"
                                                label="Hepsini Aç"
                                                inset
                                            ></v-switch>
                                        </template>

                                        <template v-slot:text>
                                            <v-table :eager="true">
                                                <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th>Role</th>
                                                    <th>Detail</th>
                                                    <th>Extra</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="childRole in role['children']">
                                                    <td>
                                                        <v-switch
                                                            color="primary"
                                                            hide-details
                                                            v-model="childRole.checked"
                                                            inset
                                                        ></v-switch>
                                                    </td>
                                                    <td>
                                                        {{ childRole.route_name }}
                                                    </td>
                                                    <td>
                                                        -
                                                    </td>
                                                    <td>
                                                        -
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </v-table>
                                        </template>
                                    </v-card>
                                </v-col>
                            </template>
                        </v-row>
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
            allManagerSwitches: [],
            allUserSwitches: [],
            form: useForm({
                name: (this.package.name) ? this.package.name : '',
                userRoles: this.userRoles,
                managerRoles: this.managerRoles
            }),
        }
    },
    props: {
        package: {
            type: Object,
            default: {}
        },
        managerRoles: {
            type: Object,
            default: {}
        },
        userRoles: {
            type: Object,
            default: {}
        },
        show: {
            type: Boolean,
            default: false
        },
    },
    methods: {
        allManagerSwitchChange(key) {
            this.managerRoles[key].children.forEach((value, index) => {
                value.checked = this.allManagerSwitches[key];
            });
        },
        allUserSwitchChange(key) {
            this.userRoles[key].children.forEach((value, index) => {
                value.checked = this.allUserSwitches[key];
            });
        },
        isCreate() {
            return typeof (this.package.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.package.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        save() {
            if (this.isUpdate()) {
                this.form.put(route('admin.package.update', this.package.id));
            } else if (this.isCreate()) {
                this.form.post(route('admin.package.store'));
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
                title: 'Group List',
                disabled: false,
                href: route('admin.package.index'),
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
