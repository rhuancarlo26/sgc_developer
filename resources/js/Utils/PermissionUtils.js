import {usePage} from "@inertiajs/vue3";

const page = usePage();

let user = {};

const setUser = () => {

    user = page.props.auth?.user;

}

const hasRole = (roles) => {

    setUser();

    roles = typeof roles === 'string' ? [roles] : roles;
    return user.roles.some(r => roles.includes(r.name));

}


const can = (permissions) => {

    setUser();

    if (hasRole('Super Admin')) {
        return true;
    }

    permissions = typeof permissions === 'string' ? [permissions] : permissions;
    return user.all_permissions.some(p => permissions.includes(p));
}


const canSeePrefix = (prefix) => {

    setUser();

    if (hasRole('Super Admin')) {
        return true;
    }

    prefix = typeof prefix === 'string' ? [prefix] : prefix;
    prefix = prefix.map(p => p.replaceAll('*', ''));


    return user.all_permissions.some(p => {
        return prefix.some(pre => p.includes(pre))
    })


}

export {can, hasRole, canSeePrefix};
