export const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('ms-MY', {
    style: 'currency',
    currency: 'MYR',
    minimumFractionDigits: 2,
  }).format(amount)
}

export const formatPrice = (price: number): string => {
  return formatCurrency(price)
}
