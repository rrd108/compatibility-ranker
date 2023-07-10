import chart from './chart.json' assert { type: 'json' }

console.log(
  chart.find(d => d.girl == 'Aswini 4' && d.boy == 'Aswini 1').point == 19
)

console.log(
  chart.find(d => d.girl == 'Mirigasrisam 1' && d.boy == 'Rohini 1').point == 33
)

console.log(
  chart.find(d => d.girl == 'Punarpusam 2' && d.boy == 'Adra 1').point == '6,5'
)

console.log(
  chart.find(d => d.girl == 'Avittam 2' && d.boy == 'Utthiratadhi 1').point ==
    '8,5'
)

console.log(
  chart.find(d => d.girl == 'Revati 4' && d.boy == 'Revati 4').point == 28
)
