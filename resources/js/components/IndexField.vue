<template>
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
</template>

<script>
import { Localization } from 'laravel-nova'

export default {
  mixins: [Localization],
  props: ['resourceName', 'field'],
  data() {
    return {
      list: false,
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
    }
  },
  // computed: {
  //   fieldValue() {
  //     return this.field.displayedAs || this.field.value
  //   },
  // }
}
</script>
