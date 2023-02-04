#include <targets/AT91SAM7X256.h>
#include "lcd.h"

int main(void){
  //LCD_BL
  PMC_PCER = 1<<3;
  PIOB_PER = 1<<20;
  PIOB_OER = 1<<20;

  //TIMER
  PMC_PCER |= 1<<12;
  TC0_CCR = 1<<1;
  TC0_SR;
  TC0_CMR = 0b011 | 1<<14;
  TC0_RC = 37500;
  TC0_CCR = 1<<0 | 1<<2;

  InitLCD();
  SetContrast(30);
  LCDClearScreen();

  while(1){
    for(int i = 0; i <= 130; i++){
      while(!(TC0_SR & 1<<4));
      
      LCDSetRect(130,0,130-i,130,FILL,RED);
    }
    LCDClearScreen();
  }
}