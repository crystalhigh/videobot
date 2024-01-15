<template>
    <div class="advertiser-transaction mt-4">
			<div class="publisher-earned text-center pb-3">{{ `Balance: $${balance}` }}</div>
			<div class="flow-headers">
				<span :class="mode == 'earned' ? 'active' : ''">
					<a @click="handleMode('earned')">Earned</a>
				</span>
				<span :class="mode == 'paid' ? 'active' : ''">
					<a @click="handleMode('paid')">Paid</a>
				</span>
				<span :class="mode == 'pending' ? 'active' : ''">
					<a @click="handleMode('pending')">Pending</a>
				</span>
			</div>
			<div class="publisher-earned text-center pb-3">{{ `$${mode == 'earned' ? earned : (mode == 'paid' ? paid : pending)}` }}</div>
			<b-table
				id="transactin-publisher-table"
				hover
				bordered
				sticky-header
				head-variant="light"
				:fields="fields"
				:items="mode == 'earned' ? items_pubEarned : (mode == 'paid' ? items_pubPaid : items_pubPending)"
				class="w-100 text-center"
				v-if="(mode =='earned' && items_pubEarned.length) || (mode =='paid' && items_pubPaid.length) || (mode =='pending' && items_pubPending.length) "
			>
				<template #cell(index)="row">
					<span>{{ row.index + 1 }}</span>
				</template>
				<template #cell(cost_per_campaign)="row">
					{{ `$${row.value}` }}
				</template>
				<template #cell(website_url)="row">
					<span class="d-block" v-for="(val, index) in row.value">{{ val }}</span>
				</template>
				<template #cell(period)="row">
					<span class="d-block" v-for="(val, index) in row.value">{{ val }}{{ index == 0 ? ' ~' : '' }}</span>
				</template>
				<template #cell(sum)="row">
					{{ `$${row.value}` }}
				</template>
			</b-table>
			<b-pagination
				v-model="currentPage"
				:total-rows="total"
				:per-page="perPage"
				aria-controls="transactin-publisher-table"
				first-text="First"
				prev-text="Prev"
				next-text="Next"
				last-text="Last"
				align="right"
				v-if="(mode =='earned' && items_pubEarned.length) || (mode =='paid' && items_pubPaid.length) || (mode =='pending' && items_pubPending.length) "
			></b-pagination>
    </div>
  </template>
  
  <style lang="scss" moduled>
		.publisher-earned { 
			font-size: 24px;
			font-weight: 700;
		}
		.b-table-sticky-header {
			max-height: 100%;	
		}
  </style>
  
  <script>
  export default {
    name: 'publisher',
		props: {
			items_pubEarned: { type: Array, default: [] },
			items_pubPending: { type: Array, default: [] },
			items_pubPaid: { type: Array, default: [] },
			total: { type: Number, default: 0 },
			perPage: { type: Number, default: 0 },
			balance: { type: Number, default: 0 },
		},
		watch: {
			currentPage(newVal, oldVal) {
				this.$emit('handlePage', newVal);
			}
		},
    data() {
      return {
				fields: [
					{ key: 'index', label: '#', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe" } },
					{ key: 'advertiser_name', label: 'Advertiser name', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'campaign_name', label: 'Offer', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'cost_per_campaign', label: 'Cost per offer', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'website_url', label: 'Website', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'impressions', label: 'Impressions', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'plays', label: 'Plays', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'leads', label: 'Leads', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'sum', label: 'Sum', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
					{ key: 'period', label: 'Period', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
				],
				currentPage: 1,
				mode: 'earned',
      };
    },
    computed: {
			earned() {
				return this.items_pubEarned.reduce((res, val) => res + val.cost_per_campaign * val.leads, 0);
			},
			paid() {
				return this.items_pubPaid.reduce((res, val) => res + val.cost_per_campaign * val.leads, 0);
			},
			pending() {
				return this.items_pubPending.reduce((res, val) => res + val.cost_per_campaign * val.leads, 0);
			}
		},
    methods: {
			handleMode(mode) {
				this.mode = mode;
			}
    }
  };
  </script>
  