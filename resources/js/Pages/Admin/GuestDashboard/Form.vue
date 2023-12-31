<template>
    <AdminAppLayout>
        <v-container :fluid="true">
            <v-card>
                <v-card-title>
                    Guest Dashboard Form
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
                            v-model="form.company_id"
                            label="Company"
                            :items="companies"
                            :clearable="true"
                            variant="outlined"
                            item-title="name"
                            item-value="id"
                        ></v-autocomplete>
                        <v-text-field
                            variant="outlined"
                            v-model="form.url"
                            label="Url"
                            required
                            :error-messages="form.errors.url"
                        >
                        </v-text-field>
                        <v-container fluid class="d-flex flex-nowrap overflow-auto ">
                            <v-card class="black text-center grey d-flex flex-column align-center justify-center" id="trash" outlined>
                                <div>
                                    <v-icon style="font-size: 200%;">mdi-delete</v-icon>
                                    <h3>Delete</h3>
                                </div>
                            </v-card>
                            <v-card class="black text-center  bg-green-darken-1 d-flex flex-column align-center justify-center" outlined>
                                <div class="newWidget grid-stack-item" id="toolbar" style="cursor:pointer" gs-id="toolbar" gs-w="12" gs-h="2">
                                    <div class="grid-stack-item-content bg-green-darken-1">
                                        <v-icon style="font-size: 200%;">mdi-page-layout-header</v-icon>
                                        <h3>Toolbar</h3>
                                    </div>
                                </div>
                            </v-card>
                            <v-card class="black text-center  bg-green-darken-1 d-flex flex-column align-center justify-center" outlined>
                                <div class="newWidget grid-stack-item" id="menu" style="cursor:pointer" gs-id="menu" gs-w="2" gs-h="7" gs-max-w="3">
                                    <div class="grid-stack-item-content bg-green-darken-1">
                                        <v-icon style="font-size: 200%;">mdi-menu</v-icon>
                                        <h3>Menu</h3>
                                    </div>
                                </div>
                            </v-card>
                            <v-card class="black text-center  bg-green-darken-1 d-flex flex-column align-center justify-center" outlined>
                                <div class="newWidget grid-stack-item" id="articles" style="cursor:pointer" gs-id="articles" gs-w="3" gs-h="6">
                                    <div class="grid-stack-item-content bg-green-darken-1">
                                        <v-icon style="font-size: 200%;">mdi-text</v-icon>
                                        <h3>Articles</h3>
                                    </div>
                                </div>
                            </v-card>
                            <v-card class="black text-center  bg-green-darken-1 d-flex flex-column align-center justify-center" outlined>
                                <div class="newWidget grid-stack-item" id="slider" style="cursor:pointer" gs-id="slider" gs-w="8" gs-h="6">
                                    <div class="grid-stack-item-content bg-green-darken-1">
                                        <v-icon style="font-size: 200%;">mdi-unfold-more-vertical</v-icon>
                                        <h3>Slider</h3>
                                    </div>
                                </div>
                            </v-card>
                            <v-card v-for="index in 10" :key="index" class="black" outlined>
                                <!-- Kart içeriği buraya gelebilir -->
                            </v-card>
                        </v-container>
                        <v-row class="mt-3 mb-3">
                            <v-col md="12">
                                <div class="grid-stack p-3" ref="grid">
                                    <div class="grid-stack-item" v-for="(item, index) in this.form.content" :key="index"
                                         :item="item"
                                         :gs-w="item.w"
                                         :gs-h="item.h"
                                         :gs-x="item.x"
                                         :gs-y="item.y"
                                         :gs-id="item.id"
                                         :draggable="true"
                                        :resizable="true"
                                    >
                                        <div class="grid-stack-item-content">

                                            <v-btn v-on:click="test(123)">Ayarlar</v-btn>
                                            <!--                                            <Menu v-if="item.id=='menu'"></Menu>-->
                                            <!--                                            <Toolbar v-if="item.id=='toolbar'"></Toolbar>-->
                                            <!--                                            <Articles v-if="item.id=='articles'"></Articles>-->
                                            <!--                                            <Slider v-if="item.id=='slider'"></Slider>-->
                                        </div>
                                        <div class="ui-resizable-handle ui-resizable-se" style="z-index: 100; user-select: none;"></div>
                                    </div>
                                </div>
                            </v-col>
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
import 'gridstack/dist/gridstack.min.css';
import "gridstack/dist/gridstack.css";
import {GridStack} from 'gridstack';
import {createApp } from 'vue';
import Menu from "../../../Components/Widgets/Menu.vue"
import Toolbar from "../../../Components/Widgets/Toolbar.vue"
import Articles from "../../../Components/Widgets/Articles.vue"
import Slider from "../../../Components/Widgets/Slider.vue"

export default {
    name: "Form",
    components: {Menu, Toolbar, Articles, Slider},
    data() {
        return {
            form: useForm({
                name: (this.guestDashboard.name) ? this.guestDashboard.name : '',
                content: (this.guestDashboard.content) ? this.guestDashboard.content : [],
                url: (this.guestDashboard.url) ? this.guestDashboard.url : '',
                company_id: (this.guestDashboard.company_id) ? this.guestDashboard.company_id : '',
                is_active: (this.guestDashboard.is_active) ? this.guestDashboard.is_active : 1,
            }),
        }
    },
    props: {
        guestDashboard: {
            type: Object,
            default: {}
        },
        companies: {
            type: Object,
            default: {}
        },
        show: {
            type: Boolean,
            default: false
        },
    },
    methods: {
        updateInterface() {
            // Sadece arayüzü güncelle
        },
        test(id) {
            alert(id);
            this.$nextTick(() => {
                // Adding the component back in
             //   this.renderComponent = true;
            });
        },
        isCreate() {
            return typeof (this.guestDashboard.id) === 'undefined';
        },
        isUpdate() {
            return typeof (this.guestDashboard.id) !== 'undefined' && !this.isShow();
        },
        isShow() {
            return this.show;
        },
        save() {
            this.form.content = this.grid.save();
             if (this.isUpdate()) {
                 this.form.put(route('admin.guest_dashboard.update', this.guestDashboard.id));
             } else if (this.isCreate()) {
                this.form.post(route('admin.guest_dashboard.store'));
             }
        },
        setGridPositionAndSize() {
            // Grid yüklendikten sonra pozisyonları ve boyutları ayarla
            if (this.grid && this.grid.el) {
                this.form.content.forEach((item, index) => {
                    const gridElement = this.grid.el.children[index];
                    if (gridElement) {
                        this.grid.update(gridElement, item.x, item.y, item.w, item.h);
                    }
                });
            }
        },
        compact() {
            this.grid.compact();
        },
    },
    mounted() {
        this.grid = GridStack.init({
            float: true,
            cellHeight: 70,
            acceptWidgets: true,
            removable: '#trash',
        });
        GridStack.setupDragIn('.newWidget', {
            appendTo: 'body',
            helper: function (event) {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'grid-stack-item-content';
                emptyDiv.setAttribute('gs-w', '2');
                emptyDiv.setAttribute('gs-h', '1');
                emptyDiv.setAttribute('gs-max-w', '3');
                emptyDiv.setAttribute('gs-id', 'foo');
                return emptyDiv;
            },
        });

        let app = createApp(Toolbar);
        let vm = app.mount(document.createElement('div'));
        const toolbar = vm.$el.outerHTML;
        app = createApp(Menu);
        vm = app.mount(document.createElement('div'));
        const menu = vm.$el.outerHTML;
        app = createApp(Articles);
        vm = app.mount(document.createElement('div'));
        const articles = vm.$el.outerHTML;
        app = createApp(Slider);
        vm = app.mount(document.createElement('div'));
        const slider = vm.$el.outerHTML;
        // console.log(slider);
        // this.grid.load(this.items);
        this.grid.on('added removed change', function (e, items) {
            if (e.type === 'added') {
                const addedItem = e.detail[0];
                if (!addedItem.el.hasAttribute('data-added')) {
                    if (addedItem.el.id === 'toolbar') {
                        addedItem.el.firstChild.innerHTML = toolbar;
                    }
                    if (addedItem.el.id === 'menu') {
                        addedItem.el.firstChild.innerHTML = menu;
                    }
                    if (addedItem.el.id === 'articles') {
                        addedItem.el.firstChild.innerHTML = articles;
                    }
                    if (addedItem.el.id === 'slider') {
                        addedItem.el.firstChild.innerHTML = slider;
                    }
                    addedItem.el.firstChild.classList.remove('bg-green-darken-1');
                  //  this.form.content = this.grid.save();
                  //   this.grid.removeWidget(addedItem.el);

                    this.setGridPositionAndSize();
                }
            }
        }.bind(this));
        this.setGridPositionAndSize();

        this.$page.props.breadcrumbs = [
            {
                title: 'Dashboard',
                disabled: false,
                href: route('admin.home'),
            },
            {
                title: 'Guest Dashboard List',
                disabled: false,
                href: route('admin.guest_dashboard.index'),
            },
            {
                title: 'Guest Dashboard Form',
                disabled: true,
            },
        ]
    },
}
</script>


<style>
.overflow-auto {
    overflow-x: auto;
    white-space: nowrap;
    display: flex;
}

.black {
    background-color: black;
    height: 200px;
    width: 200px;
    margin-right: 8px;
    min-width: 200px;
    max-width: 200px;
}

/* Optional styles for demos */
.btn-primary {
    color: #fff;
    background-color: #007bff;
}

.btn {
    display: inline-block;
    padding: .375rem .75rem;
    line-height: 1.5;
    border-radius: .25rem;
}

a {
    text-decoration: none;
}

h1 {
    font-size: 2.5rem;
    margin-bottom: .5rem;
}

.grid-stack {
    background: #FAFAD2;
    min-height: 100px !important;
}

.grid-stack-item-content {
    text-align: center;
    background-color: #18bc9c;
}

.grid-stack-item-removing {
    opacity: 0.5;
}

.trash {
    height: 100px;
    background: rgba(255, 0, 0, 0.1) center center url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjY0cHgiIGhlaWdodD0iNjRweCIgdmlld0JveD0iMCAwIDQzOC41MjkgNDM4LjUyOSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDM4LjUyOSA0MzguNTI5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQxNy42ODksNzUuNjU0Yy0xLjcxMS0xLjcwOS0zLjkwMS0yLjU2OC02LjU2My0yLjU2OGgtODguMjI0TDMwMi45MTcsMjUuNDFjLTIuODU0LTcuMDQ0LTcuOTk0LTEzLjA0LTE1LjQxMy0xNy45ODkgICAgQzI4MC4wNzgsMi40NzMsMjcyLjU1NiwwLDI2NC45NDUsMGgtOTEuMzYzYy03LjYxMSwwLTE1LjEzMSwyLjQ3My0yMi41NTQsNy40MjFjLTcuNDI0LDQuOTQ5LTEyLjU2MywxMC45NDQtMTUuNDE5LDE3Ljk4OSAgICBsLTE5Ljk4NSw0Ny42NzZoLTg4LjIyYy0yLjY2NywwLTQuODUzLDAuODU5LTYuNTY3LDIuNTY4Yy0xLjcwOSwxLjcxMy0yLjU2OCwzLjkwMy0yLjU2OCw2LjU2N3YxOC4yNzQgICAgYzAsMi42NjQsMC44NTUsNC44NTQsMi41NjgsNi41NjRjMS43MTQsMS43MTIsMy45MDQsMi41NjgsNi41NjcsMi41NjhoMjcuNDA2djI3MS44YzAsMTUuODAzLDQuNDczLDI5LjI2NiwxMy40MTgsNDAuMzk4ICAgIGM4Ljk0NywxMS4xMzksMTkuNzAxLDE2LjcwMywzMi4yNjQsMTYuNzAzaDIzNy41NDJjMTIuNTY2LDAsMjMuMzE5LTUuNzU2LDMyLjI2NS0xNy4yNjhjOC45NDUtMTEuNTIsMTMuNDE1LTI1LjE3NCwxMy40MTUtNDAuOTcxICAgIFYxMDkuNjI3aDI3LjQxMWMyLjY2MiwwLDQuODUzLTAuODU2LDYuNTYzLTIuNTY4YzEuNzA4LTEuNzA5LDIuNTctMy45LDIuNTctNi41NjRWODIuMjIxICAgIEM0MjAuMjYsNzkuNTU3LDQxOS4zOTcsNzcuMzY3LDQxNy42ODksNzUuNjU0eiBNMTY5LjMwMSwzOS42NzhjMS4zMzEtMS43MTIsMi45NS0yLjc2Miw0Ljg1My0zLjE0aDkwLjUwNCAgICBjMS45MDMsMC4zODEsMy41MjUsMS40Myw0Ljg1NCwzLjE0bDEzLjcwOSwzMy40MDRIMTU1LjMxMUwxNjkuMzAxLDM5LjY3OHogTTM0Ny4xNzMsMzgwLjI5MWMwLDQuMTg2LTAuNjY0LDguMDQyLTEuOTk5LDExLjU2MSAgICBjLTEuMzM0LDMuNTE4LTIuNzE3LDYuMDg4LTQuMTQxLDcuNzA2Yy0xLjQzMSwxLjYyMi0yLjQyMywyLjQyNy0yLjk5OCwyLjQyN0gxMDAuNDkzYy0wLjU3MSwwLTEuNTY1LTAuODA1LTIuOTk2LTIuNDI3ICAgIGMtMS40MjktMS42MTgtMi44MS00LjE4OC00LjE0My03LjcwNmMtMS4zMzEtMy41MTktMS45OTctNy4zNzktMS45OTctMTEuNTYxVjEwOS42MjdoMjU1LjgxNVYzODAuMjkxeiIgZmlsbD0iI2ZmOWNhZSIvPgoJCTxwYXRoIGQ9Ik0xMzcuMDQsMzQ3LjE3MmgxOC4yNzFjMi42NjcsMCw0Ljg1OC0wLjg1NSw2LjU2Ny0yLjU2N2MxLjcwOS0xLjcxOCwyLjU2OC0zLjkwMSwyLjU2OC02LjU3VjE3My41ODEgICAgYzAtMi42NjMtMC44NTktNC44NTMtMi41NjgtNi41NjdjLTEuNzE0LTEuNzA5LTMuODk5LTIuNTY1LTYuNTY3LTIuNTY1SDEzNy4wNGMtMi42NjcsMC00Ljg1NCwwLjg1NS02LjU2NywyLjU2NSAgICBjLTEuNzExLDEuNzE0LTIuNTY4LDMuOTA0LTIuNTY4LDYuNTY3djE2NC40NTRjMCwyLjY2OSwwLjg1NCw0Ljg1MywyLjU2OCw2LjU3QzEzMi4xODYsMzQ2LjMxNiwxMzQuMzczLDM0Ny4xNzIsMTM3LjA0LDM0Ny4xNzJ6IiBmaWxsPSIjZmY5Y2FlIi8+CgkJPHBhdGggZD0iTTIxMC4xMjksMzQ3LjE3MmgxOC4yNzFjMi42NjYsMCw0Ljg1Ni0wLjg1NSw2LjU2NC0yLjU2N2MxLjcxOC0xLjcxOCwyLjU2OS0zLjkwMSwyLjU2OS02LjU3VjE3My41ODEgICAgYzAtMi42NjMtMC44NTItNC44NTMtMi41NjktNi41NjdjLTEuNzA4LTEuNzA5LTMuODk4LTIuNTY1LTYuNTY0LTIuNTY1aC0xOC4yNzFjLTIuNjY0LDAtNC44NTQsMC44NTUtNi41NjcsMi41NjUgICAgYy0xLjcxNCwxLjcxNC0yLjU2OCwzLjkwNC0yLjU2OCw2LjU2N3YxNjQuNDU0YzAsMi42NjksMC44NTQsNC44NTMsMi41NjgsNi41N0MyMDUuMjc0LDM0Ni4zMTYsMjA3LjQ2NSwzNDcuMTcyLDIxMC4xMjksMzQ3LjE3MnogICAgIiBmaWxsPSIjZmY5Y2FlIi8+CgkJPHBhdGggZD0iTTI4My4yMiwzNDcuMTcyaDE4LjI2OGMyLjY2OSwwLDQuODU5LTAuODU1LDYuNTctMi41NjdjMS43MTEtMS43MTgsMi41NjItMy45MDEsMi41NjItNi41N1YxNzMuNTgxICAgIGMwLTIuNjYzLTAuODUyLTQuODUzLTIuNTYyLTYuNTY3Yy0xLjcxMS0xLjcwOS0zLjkwMS0yLjU2NS02LjU3LTIuNTY1SDI4My4yMmMtMi42NywwLTQuODUzLDAuODU1LTYuNTcxLDIuNTY1ICAgIGMtMS43MTEsMS43MTQtMi41NjYsMy45MDQtMi41NjYsNi41Njd2MTY0LjQ1NGMwLDIuNjY5LDAuODU1LDQuODUzLDIuNTY2LDYuNTdDMjc4LjM2NywzNDYuMzE2LDI4MC41NSwzNDcuMTcyLDI4My4yMiwzNDcuMTcyeiIgZmlsbD0iI2ZmOWNhZSIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=) no-repeat;
}

.sidebar {
    background: rgba(0, 255, 0, 0.1);
    padding: 25px 0;
    height: 100px;
    text-align: center;
}

.sidebar .grid-stack-item {
    width: 120px;
    height: 50px;
    border: 2px dashed green;
    text-align: center;
    line-height: 35px;
    background: rgba(0, 255, 0, 0.1);
    cursor: default;
    display: inline-block;
}

.sidebar .grid-stack-item .grid-stack-item-content {
    background: none;
}

/* make nested grid have slightly darker bg take almost all space (need some to tell them apart) so items inside can have similar to external size+margin */
.grid-stack > .grid-stack-item.grid-stack-sub-grid > .grid-stack-item-content {
    background: rgba(0, 0, 0, 0.1);
    inset: 0 2px;
}

.grid-stack.grid-stack-nested {
    background: none;
    /* background-color: red; */
    /* take entire space */
    position: absolute;
    inset: 0;
    /* TODO change top: if you have content in nested grid */
}

.grid-stack-item-removing {
    opacity: 0.8;
    filter: blur(5px);
}

#trash {
    background: rgba(255, 0, 0, 0.4);
}
</style>
