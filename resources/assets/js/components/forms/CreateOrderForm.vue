<template>
	<div>
		<div class="row">
			<div class="col-sm-4">
				<div class="box box-solid">
					<div class="box-body">

						<div id="OrderAttrForm">
							<div class="form-group" style="margin-bottom: 5px">
								<div class="input-group">
									<v-select :options="customerOptions"></v-select>
									<span class="input-group-btn">
										<button type="button" class="btn btn-info btn-flat" @click="showNewCustomerModal">
											<i class="fa fa-plus"></i>
										</button>
									</span>
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 5px">
								<div class="input-group">
									<select class="form-control">
										<option value="0">Without a table</option>
									</select>
									<span class="input-group-btn">
										<button type="button" class="btn btn-info btn-flat">
											<i class="fa fa-hand-o-up"></i>
										</button>
									</span>
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 5px">
								<button type="button" class="btn btn btn-warning btn-flat btn-block">
									<i class="fa fa-cutlery"></i>
									Select Dish
								</button>
							</div>
						</div>


						<div id="cartsTable" class="well no-padding no-border table-responsive"
						style="height: 200px; overflow: auto; background: #f4f4f4; border-radius: 0; margin-bottom: 5px">
							<table class="table">
								<thead class="bg-green disabled color-palette">
									<tr>
										<th>Dish</th>
										<th>Price</th>
										<th>Qty</th>
										<th>Total</th>
										<th><i class="fa fa-trash"></i></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Test</td>
										<td>2.00</td>
										<td>2</td>
										<td>4.00</td>
										<td><i class="fa fa-trash"></i></td>
									</tr>
								</tbody>
							</table>
						</div>

						<div id="resultTable" class="table-responsive">
							<table class="table">
								<tbody>
									<tr class="bg-navy disabled color-palette">
										<td>Total Items</td>
										<td class="text-right">3</td>
										<td>Subtotal</td>
										<td class="text-right">30.00</td>
									</tr>
									<tr class="bg-navy disabled color-palette">
										<td>Discount</td>
										<td class="text-right">0.00</td>
										<td>Tax</td>
										<td class="text-right">0.00</td>
									</tr>
									<tr class="bg-red color-palette">
										<td><strong>Total Payable</strong></td>
										<td colspan="3" class="text-right"><strong>0.00</strong></td>
									</tr>
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
		</div>

		<customer-modal></customer-modal>

	</div>
</template>

<script>

	import VueSelect from 'vue-select';
	import CustomerModal from '../modals/CustomerModal.vue';

	export default {
		components: {
			'v-select': VueSelect,
			'customer-modal': CustomerModal,
		},

		data: function() {
			return {
				customerOptions: [{label: 'Walk-In Cliend', value: 0}]
			}
		},

		methods: {
			showNewCustomerModal: function() {
				$('#newCustomerModal').modal({
					backdrop: 'static',
					keyboard: false
				})
			},

			fetchCustomers()
			{
				axios.get('/api/customer')
				.then(response => {
					var customers = response.data.customers
					for (var i = 0; i < customers.length; i++) {
						this.customerOptions.push({label: customers[i].name, value: customers[i].id})
					}
				}).catch(error => {
					console.log(error.response.data);
				})
			}
		},

		mounted: function() {
			this.fetchCustomers();
		}
	}
</script>
