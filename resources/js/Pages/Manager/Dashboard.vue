<template>
    <app-layout>
        <v-container>
            <v-card>
                <v-card-title>
                    Todo List
                </v-card-title>
                <v-card-item>
                    <v-col>
                        <v-text-field
                            variant="outlined"
                            v-model="newTodo.title"
                            label="Title"
                        >
                        </v-text-field>
                        <v-textarea
                            variant="outlined"
                            v-model="newTodo.content"
                            label="Content">
                        </v-textarea>
                        <v-btn
                            variant="outlined"
                            color="primary"
                            text="Save"
                            v-on:click="todoList.push(newTodo);
                            newTodo={title:'',content:'',done:false,date: Date.now()};">

                        </v-btn>
                    </v-col>
                </v-card-item>
                <v-card-item>
                    <v-data-table
                        v-model:items-per-page="itemsPerPage"
                        :headers="headers"
                        :items="todoList"
                        :multi-sort="true"
                        :sort-by="[{
                                    'key': 'done',
                                    'order': 'asc'
                                },{
                                    'key': 'date',
                                    'order': 'desc'
                                }]"
                        class="elevation-1"
                    >
                        <template v-slot:item.delete="{ item }">
                            <v-btn variant="text"
                                   icon="mdi-delete"
                                   v-on:click="this.todoList = this.todoList.filter((value) => {return (value !== item.raw)});console.log('asd')">
                            </v-btn>
                        </template>
                        <template v-slot:item.done="{ item }">
                            <v-checkbox v-model="item.raw.done" :error="false" :hide-details="true">
                            </v-checkbox>
                        </template>
                        <template v-slot:item.content="{ item }">
                            <div :class="(item.raw.done)?'text-decoration-line-through':''">
                                <v-table>
                                    <tbody>
                                    <tr>
                                        <td class="border-0" style="border: 0">{{ item.raw.title }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ item.raw.content }}</td>
                                    </tr>
                                    </tbody>
                                </v-table>
                            </div>
                        </template>

                        <template v-slot:item.date="{ item }">
                            {{
                                new Date(item.raw.date).toLocaleDateString() + ' ' + new Date(item.raw.date).toLocaleTimeString()
                            }}
                        </template>
                    </v-data-table>
                </v-card-item>
            </v-card>
        </v-container>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/Manager/AppLayout.vue";

export default {
    name: "Dashboard",
    components: {
        AppLayout
    },
    data() {
        return {
            newTodo: {
                title: '',
                content: '',
                done: false,
                date: Date.now(),
            },
            itemsPerPage: 10,
            todoList: this.todoListJson,
            headers: [
                {sortable: false, title: 'Delete', align: 'center', key: 'delete'},
                {sortable: false, title: 'Done', align: 'center', key: 'done'},
                {sortable: false, title: 'Content', align: 'start', key: 'content'},
                {sortable: false, title: 'Date', align: 'start', key: 'date'},
            ]
        }
    },
    props: {
        todoListJson: {
            type: Array,
            default: [],
        }
    },
    watch: {
        todoList: {
            deep: true,
            handler(newValue, oldValue) {
                axios.post(route('manager.todolist'), newValue).then(response => {
                }).catch((error) => {
                    // this.$page.props.flash.error = error.response.data.message;
                });
            },
        },
    },
    mounted() {
        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: true,
                href: route('manager.home'),
            },
        ]
    }

}
</script>

<style scoped>

</style>
