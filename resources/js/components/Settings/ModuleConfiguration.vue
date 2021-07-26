<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Module Configuration
                    </span>
                </div>
            </div>
            <div class="card-body">

                <table class="table">
                    <tbody>
                        <tr v-for="module in modules" :key="module.id">
                            <td>
                                <div><strong>{{ module.name }}</strong></div>
                                <div>{{ module.description }}</div>
                            </td>
                            <td class="text-right align-middle">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" @change="updateModuleStatus(module.id)" class="custom-control-input" :id="module.id" v-model="module.enabled">
                                    <label class="custom-control-label" :for="module.id"></label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";

export default {
    mixins: [api],

    created() {
        this.apiGetModules()
            .then(({ data }) => {
                this.modules = data.data;
            });
    },

    data: () => ({
        error: false,
        modules: []
    }),

    methods: {
        updateModuleStatus(module_id) {
            this.apiToggleModules(module_id);
        }
    }
}
</script>
