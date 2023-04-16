<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Magento Api Configurations
                    </span>
                    <span class="text-primary cursor-pointer" @click="showCreateForm">
                        Create New Connection
                    </span>
                </div>
            </div>

            <div class="card-body">
                <table v-if="connections.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th>Magento Store ID</th>
                            <th>Connection Tag</th>
                            <th>Pricing Source</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="connection in connections" :key="connection.id">
                            <td>{{ connection.base_url }}</td>
                            <td>{{ connection.magento_store_id }}</td>
                            <td>
                                <template v-for="tag in connection.tags">
                                    <a class="badge text-uppercase" :key="tag.id"> {{ tag.name }} </a>
                                </template>
                            </td>
                            <td>{{ connection.warehouse?.name }}</td>
                            <td>
                                <a @click="confirmDelete(connection.id)" class="action-link text-danger">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    You have not created any Magento Api connections.
                </p>
            </div>
        </div>

        <create-modal @onCreated="getConnections"></create-modal>
    </div>
</template>

<script>
    import api from "../../mixins/api";
    import CreateModal from './MagentoApi/CreateModal.vue';

    export default {
        components: {
            CreateModal
        },

        mixins: [api],

        data: () => ({
            connections: [],
        }),

        mounted() {
            this.getConnections();
        },

        methods: {

            getConnections() {
                this.apiGetMagentoApiConnections({
                    'per_page': 100,
                    'include': 'tags,warehouse'
                })
                .then(({ data }) => {
                    this.connections = data.data;
                });
            },

            showCreateForm() {
                this.$bvModal.show('modal-create-connection')
            },

            confirmDelete(connection_id){
                this.$snotify.confirm('After delete data cannot restored', 'Are you sure?', {
                    position: 'centerCenter',
                    buttons: [
                        {
                            text: 'Yes',
                            action: (toast) => {
                                this.handleDelete(connection_id)
                                this.$snotify.remove(toast.id);
                            }
                        },
                        {text: 'Cancel'},
                    ]
                });
            },

            handleDelete(connection_id, index) {
                this.apiDeleteMagentoApiConnection(connection_id)
                    .then(() => {
                        this.getConnections()
                    });
            }
        },
    }

</script>

<style lang="scss" scoped>
    .action-link {
        cursor: pointer;
    }
</style>
