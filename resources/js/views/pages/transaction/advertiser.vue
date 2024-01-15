<template>
	<div class="advertiser-transaction mt-4">
		<div class="advertiser-paid text-center pb-3">{{ `Balance: $${balance}` }}</div>
		<div class="flow-headers">
			<span :class="mode == 'paid' ? 'active' : ''">
				<a @click="handleMode('paid')">Paid</a>
			</span>
			<span :class="mode == 'pending' ? 'active' : ''">
				<a @click="handleMode('pending')">Pending</a>
			</span>
		</div>
		<div class="advertiser-paid text-center pb-3">{{ `$${mode == 'paid' ? paid : pending}` }}</div>
		<b-table
			id="transactin-advertiser-table"
			hover
			bordered
			sticky-header
			head-variant="light"
			:fields="fields"
			:items="mode == 'paid' ? items_advPaid : items_advPending"
			class="w-100 text-center"
			v-if="(mode == 'paid' && items_advPaid.length) || (mode == 'pending' && items_advPending.length)"
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
			aria-controls="transactin-advertiser-table"
			first-text="First"
			prev-text="Prev"
			next-text="Next"
			last-text="Last"
			align="right"
		></b-pagination>
	</div>
</template>
  
<style lang="scss" moduled>
	.advertiser-paid {
		font-size: 24px;
		font-weight: 700;
	}
	.b-table-sticky-header {
		max-height: 100%;	
	}
</style>

<script>
export default {
	name: 'advertiser',
	props: {
		items_advPaid: { type: Array, default: [] },
		items_advPending: { type: Array, default: [] },
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
        { key: 'publisher_name', label: 'Publisher name', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'campaign_name', label: 'Campaign name', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'cost_per_campaign', label: 'Cost per campaign', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'website_url', label: 'Website', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'impressions', label: 'Impressions', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'plays', label: 'Plays', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'leads', label: 'Leads', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'sum', label: 'Sum', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'period', label: 'Period', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
      ],
			currentPage: 1,
			mode: 'paid',
		};
	},
	computed: {
		paid() {
			return this.items_advPaid.reduce((res, val) => res + val.cost_per_campaign * val.leads, 0);
		},
		pending() {
			return this.items_advPending.reduce((res, val) => res + val.cost_per_campaign * val.leads, 0);
		}
	},
	methods: {
		handleMode(mode) {
			this.mode = mode;
		}
	}
};
</script>
  