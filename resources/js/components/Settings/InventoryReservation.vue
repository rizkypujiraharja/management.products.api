<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Inventory Reservations
                    </span>
                </div>
            </div>
            <div class="card-body">
                <p>Reserves stock for open orders.</p>

                <ValidationObserver ref="form">
                    <form class="form" @submit.prevent="submit" ref="loadingContainer">
                        <div class="form-group row">
                            <label for="create-warehouse_id" class="col-sm-3 col-form-label">Warehouse</label>
                            <div class="col-sm-9">
                                <ValidationProvider vid="warehouse_id" name="warehouse_id" v-slot="{ errors }">
                                    <select v-model="configuration.warehouse_id" :class="{
                                            'form-control': true,
                                            'is-invalid': errors.length > 0,
                                        }"
                                        id="create-warehouse_id" required>
                                        <option value=""></option>
                                        <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                                            {{ warehouse.name }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ errors[0] }}
                                    </div>
                                </ValidationProvider>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </ValidationObserver>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";
import Vue from "vue";
import helpers from "../../mixins/helpers";
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import Loading from '../../mixins/loading-overlay';

export default {
    components: {
        ValidationObserver, ValidationProvider
    },

    mixins: [api, helpers, Loading],

    mounted() {
        this.loadWarehouses();
        this.loadReservationWarehouse();
    },

    data: () => ({
        warehouses: [],
        configuration: {}
    }),

    methods: {
        loadWarehouses() {
            this.apiGetWarehouses({
                    'per_page': 999,
                    'sort': 'name',
                })
                .then(({ data }) => {
                    this.warehouses = data.data;
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        loadReservationWarehouse() {
            this.apiGetInventoryReservationsConfig()
                .then(({ data }) => {
                    this.configuration = data.data;
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        submit() {
            this.showLoading();
            let data = {
                warehouse_id: this.configuration.warehouse_id
            };
            this.apiUpdateInventoryReservationsConfig(this.configuration.id, data)
                .then(({ data }) => {
                    this.notifySuccess('Reservation warehouse updated');
                })
                .catch((error) => {
                    if (error.response) {
                        if (error.response.status === 422) {
                            this.$refs.form.setErrors(error.response.data.errors);
                        }
                    }
                })
                .finally(() => {
                    this.hideLoading();
                });
        }
    },
}
</script>
