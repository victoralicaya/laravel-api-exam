import { createRouter, createWebHistory } from "vue-router";

import Home from './pages/Home.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import AddTask from './components/AddTask.vue';
import EditTask from './components/EditTask.vue';
import ViewTask from './components/ViewTask.vue';

export const routes = [
    {
        name: 'Login',
        path: '/',
        component: Login,
        beforeEnter: checkBackToLogin,
    },
    {
        name: 'Register',
        path: '/register',
        component: Register,
        beforeEnter: checkBackToLogin,
    },
    {
        name: 'Home',
        path: '/home',
        component: Home,
        beforeEnter: checkLogin,
    },
    {
        name: 'AddTask',
        path: '/add-task',
        component: AddTask,
        beforeEnter: checkLogin,
    },
    {
        name: 'EditTask',
        path: '/edit-task/:id',
        component: EditTask,
        beforeEnter: checkLogin,
    },
    {
        name: 'ViewTask',
        path: '/view-task/:id',
        component: ViewTask,
        beforeEnter: checkLogin,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

function checkLogin(to, from, next) {
    let access_token = localStorage.getItem("access_token");
    access_token ? next() : next('/');
}

function checkBackToLogin(to, from, next) {
    let access_token = localStorage.getItem("access_token");
    access_token ? next({path: '/home'}) : next();
}

export default router;
