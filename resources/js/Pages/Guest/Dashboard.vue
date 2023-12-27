<template>
    <div>
        <v-row>
            <v-col cols="1">
                <v-row align="center" justify="center" style="width:200px;height:200px;">
                    <v-col class="fill-height" height="500">
                        <v-card class="text-center  bg-green-darken-1 d-flex flex-column align-center justify-center" height="100%">
                            <div class="newWidget grid-stack-item" id="toolbar" style="cursor:pointer" gs-w="12" gs-h="2">
                                <div class="grid-stack-item-content bg-green-darken-1">
                                    <v-icon style="font-size: 200%;">mdi-page-layout-header</v-icon>
                                    <h3>Toolbar</h3>
                                </div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="1">
                <v-row align="center" justify="center" style="width:200px;height:200px;">
                    <v-col class="fill-height" height="500">
                        <v-card class="text-center  bg-green-darken-1 d-flex flex-column align-center justify-center" height="100%">
                            <div class="newWidget grid-stack-item" id="menu" style="cursor:pointer" gs-w="2" gs-h="7" gs-max-w="3">
                                <div class="grid-stack-item-content  bg-green-darken-1">
                                    <v-icon style="font-size: 200%;">mdi-menu</v-icon>
                                    <h3>Menü</h3>
                                </div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="1">
                <v-row align="center" justify="center" style="width:200px;height:200px;">
                    <v-col class="fill-height" height="500">
                        <v-card class="text-center grey d-flex flex-column align-center justify-center" height="100%" id="trash">
                            <div>
                                <v-icon style="font-size: 200%;">mdi-delete</v-icon>
                                <h3>Delete</h3>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
            <!--                <div style="width:200px;height:200px; background-color: red;" class="float-left ms-3 text-center align-center">-->

            <!--                </div>-->
            <!--                <div style="width:200px;height:200px; background-color: red;" class="float-left ms-3">-->
            <!--                    <v-btn v-on:click="compact">-->
            <!--                        compact-->
            <!--                    </v-btn>-->
            <!--                </div>-->
            <!--                <div id="trash" style="padding: 5px; margin-bottom: 15px; width:100px;height:100px;" class="text-center">-->

            <!--                </div>-->
            <!--                <div style="width:200px;background-color: red">-->

            <!--                                <div style=" width:200px;height:200px;">-->
            <!--                                    <div class="newWidget grid-stack-item" gs-w="2" gs-h="5" gs-max-w="3">-->
            <!--                                        <div class="grid-stack-item-content">-->
            <!--                                            <div style="height:100px;width:200px;">-->
            <!--                                                <div>-->
            <!--                                                    <v-icon style="font-size: 200%;">mdi-plus</v-icon>-->
            <!--                                                </div>-->
            <!--                                                <div>-->
            <!--                                                    <span>Drag me in the dashboard!</span>-->
            <!--                                                </div>-->
            <!--                                            </div>-->
            <!--                                        </div>-->
            <!--                                    </div>-->

            <!--                                </div>-->
            <!--                </div>-->
            <v-col md="12">
                <div class="grid-stack p-3" ref="grid">
                    <!--                    <div class="grid-stack-item" v-for="(item, index) in items" :key="index" :item="item"-->
                    <!--                         :gs-w="item.w" :gs-h="item.h" :gs-x="item.x" :gs-y="item.y">-->
                    <!--                        <div class="grid-stack-item-content">-->
                    <!--                            <test></test>-->
                    <!--                        </div>-->
                    <!--                        <div class="ui-resizable-handle ui-resizable-se" style="z-index: 100; user-select: none;"></div>-->
                    <!--                    </div>-->
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import 'gridstack/dist/gridstack.min.css';
import "gridstack/dist/gridstack.css";
import {GridStack} from 'gridstack';
import {createApp} from 'vue';
import Menu from "../../Components/Widgets/menu.vue"
import Toolbar from "../../Components/Widgets/Toolbar.vue"

export default {
    components: {Menu, Toolbar},
    data() {
        return {
            grid: null,
            items: [
                {x: 0, y: 0, w: 4, h: 2},
                {
                    x: 4,
                    y: 0,
                    w: 4,
                    h: 4,
                    noMove: true,
                    noResize: true,
                    locked: true,
                },
                {
                    x: 8,
                    y: 0,
                    w: 2,
                    h: 2,
                    minW: 2,
                    noResize: true,
                },
                {x: 10, y: 0, w: 2, h: 2},
                {x: 0, y: 2, w: 2, h: 2},
                {x: 2, y: 2, w: 2, h: 4},
                {x: 8, y: 2, w: 4, h: 2},
                {x: 0, y: 4, w: 2, h: 2, widget: 1,},
                {x: 4, y: 4, w: 4, h: 2},
                {x: 8, y: 4, w: 2, h: 2},
                {x: 10, y: 4, w: 2, h: 2, widget: 1,},
            ],
        }
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

        // this.grid.load(this.items);
        this.grid.on('added removed change', function (e, items) {
            console.log(items);
            //  e.detail.el.innerHTML='<div class="grid-stack-item-content" style="padding: 5px;"> 5 </div><div class="ui-resizable-handle ui-resizable-se" style="z-index: 100; user-select: none;"></div>'
            if (e.type === 'added') {
                const addedItem = e.detail[0];
                if (!addedItem.el.hasAttribute('data-added')) {
                    if (addedItem.el.id === 'toolbar') {
                        addedItem.el.firstChild.innerHTML = toolbar;
                    }
                    if (addedItem.el.id === 'menu') {
                        addedItem.el.firstChild.innerHTML = menu;
                    }
                    addedItem.el.firstChild.classList.remove('bg-green-darken-1');
                }
            }
        }.bind(this));
        this.setGridPositionAndSize();
    },
    methods: {
        setGridPositionAndSize() {
            // Grid yüklendikten sonra pozisyonları ve boyutları ayarla
            if (this.$refs.grid && this.$refs.grid.$el) {
                this.items.forEach((item, index) => {
                    const gridElement = this.$refs.grid.$el.children[index];
                    if (gridElement) {
                        this.grid.update(gridElement, item.x, item.y, item.w, item.h);
                    }
                });
            }
        },
        selectItem(index) {
            console.log('Item selected:', index);
        },
        compact() {
            this.grid.compact();
        },
    }
};
</script>
<style>
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
