<template>
    <ManagerAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    User Role Group Form
                </v-card-title>
                <v-card-text>
                    <v-form v-on:submit.prevent="save" :disabled="isShow()">
                        <v-text-field
                            variant="outlined"
                            v-model="form.name"
                            placeholder="Name"
                            required
                            :error-messages="form.errors.name"
                        >
                        </v-text-field>
                        <v-row>
                            <template v-for="(role,key) in this.form.userRoles">
                                <v-col cols="12" class="mb-3">
                                    <v-card>
                                        <template v-slot:title>
                                            {{ role['route_name'] }}
                                        </template>

                                        <template v-slot:subtitle>
                                            <v-switch
                                                v-model="allSwitches[key]"
                                                color="success"
                                                class="me-2"
                                                label="Hepsini Aç"
                                                v-on:change="allSwitchChange(key)"
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
                </v-card-text>
            </v-card>
        </v-container>
    </ManagerAppLayout>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "Form",
    data() {
        return {
            allSwitches: [],
            form: useForm({
                name: (this.roleGroup.name) ? this.roleGroup.name : '',
                userRoles: this.userRoles
            }),
        }
    },
    props: {
        roleGroup: {
            type: Object,
            default: {}
        },
        userRoles: {
            type: Object,
            default: {}
        },
        values: {
            type: Object,
            default: {}
        },
        show: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        isCreate() {
            return typeof (this.roleGroup.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.roleGroup.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        allSwitchChange(key) {
            this.userRoles[key].children.forEach((value, index) => {
                value.checked = this.allSwitches[key];
            });
        },
        save() {
            if (this.isUpdate()) {
                this.form.put(route('manager.user_role_group.update', this.roleGroup.id));
            } else if (this.isCreate()) {
                this.form.post(route('manager.user_role_group.store'));
            }
        },
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
                disabled: false,
                href: route('manager.user_role_group.index'),
            },
            {
                title: 'Role Group Form',
                disabled: true,
            },
        ]
    }
}
</script>

<style scoped>

</style>
