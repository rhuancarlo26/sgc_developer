import {useToast} from "vue-toastification";

const toast = useToast();

export default (e) => {
    console.log(e);

    if (e.response.status === 403) {
        toast.error(e.response.data.content)
    }

    if (e.response.data?.errors) {
        Object.values(e.response?.data.errors).forEach(error => {
            toast.error(error[0])
        });
    }
}
