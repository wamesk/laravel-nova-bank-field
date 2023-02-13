<template>
  <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0 component-panel-item">
    <div class="md:w-1/4 md:py-3">
      <h4 class="font-normal">
        <span>{{ field.name }}</span>
      </h4>
    </div>

    <div class="md:w-3/4 md:py-3 break-all lg:break-words">
      <div v-if="list" class="list">
        <div v-for="item in bank" class="item">
          <p><strong>{{ item.name }}</strong></p>
          <p>{{ __('iban.title', {'iban': item.iban}) }}</p>
          <p>
            {{ __('bban.title', {'account_number': item.bban, 'code': item.bank_code}) }}
            {{ __('bic.title', {'bic': item.bic}) }}
          </p>
        </div>
      </div>

      <div v-else-if="bank">
        <p><strong>{{ bank.name }}</strong></p>
        <p>{{ __('iban.title', {'iban': bank.iban}) }}</p>
        <p>
          {{ __('bban.title', {'account_number': bank.bban, 'code': bank.bank_code}) }}
          {{ __('bic.title', {'bic': bank.bic}) }}
        </p>
      </div>

      <div v-else>–</div>
    </div>
  </div>

<!--  <PanelItem :index="index" :field="field" />-->
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
  data() {
    return {
      bank: null
    }
  },
  mounted() {
    this.prepareBank()
  },
  methods: {
    prepareBank() {
      let bank = this.field.value

      // FIX for whitecube/nova-flexible-content
      if (bank.length && !bank.startsWith('{')) {
        let list = []

        JSON.parse(bank).forEach(function (item) {
          list.push(JSON.parse('{' + item.attributes.bank + '}'))
        })

        bank = list
        this.list = true
      } else if (bank.startsWith('{')) {
        bank = JSON.parse(bank)
      }

      this.bank = bank

      // this.bank = '<p><strong>' + bank.name + '</strong></p>'
      //     + '<p>' + __('iban.title', { iban: bank.iban }) + '</p>'
      //     + '<p>' + __('bban.title', { account_number: bank.bban, 'code': bank.bank_code })
      //     + __('bic.title', { bic: bank.bic}) + '</p>';
    }
  }
}
</script>
