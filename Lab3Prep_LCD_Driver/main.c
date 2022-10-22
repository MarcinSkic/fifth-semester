#include <avr/io.h>
#include <util/delay.h>
#define LCD_DDR DDRD
#define LCD_PORT PORTD
#define LCD_RS 0
#define LCD_E 1

void LCD_writeUpperBits(char byte){
	LCD_PORT |= (1<<LCD_E);
	asm volatile("nop");
	LCD_PORT = (byte & 0xF0) | (LCD_PORT & 0x0F);
	asm volatile("nop");
	LCD_PORT &= ~(1<<LCD_E);
	asm volatile("nop");
}

void LCD_writeByte(char byte){
	_delay_ms(1);

	LCD_PORT |= (1<<LCD_E);	//Poinformowanie LCD o rozpocz�ciu transmisji
	asm volatile("nop");	//Op�nienia �eby wy�wietlacz mia� czas przetworzy� informacje
	LCD_PORT = (byte & 0xF0) | (LCD_PORT & 0x0F); //Przes�anie 4 starszych bit�w danych z zachowaniem aktualnych ustawie� wy�wietlacza (bitu E i RS) dzi�ki sumie
	asm volatile("nop");
	LCD_PORT &= ~(1<<LCD_E); //Poinformowanie LCD o zako�czeniu transmisji

	asm volatile("nop");

	LCD_PORT |= (1<<LCD_E);
	asm volatile("nop");
	LCD_PORT = ((byte & 0x0F)<<4) | (LCD_PORT & 0x0F);	//Przes�anie 4 m�odszych bit�w danych (u�ywaj�c przesuni�cia na pozycj� 4 starszych) z zachowaniem aktualnych ustawie� wy�wietlacza (bitu E i RS) dzi�ki sumie
	asm volatile("nop");
	LCD_PORT &= ~(1<<LCD_E);

	_delay_ms(1);
}

void LCD_writeInstruction(char instruction){
	LCD_PORT &= ~(1<<LCD_RS);

	LCD_writeByte(instruction);

	LCD_PORT |= 1<<LCD_RS;
}

void LCD_clear(){
	LCD_writeInstruction(0x01);
}

void LCD_writeChar(char character){
	LCD_writeByte(character);
}

void LCD_writeText(char text[], int length){
	for(int i = 0; i < length; i++){
		LCD_writeChar(text[i]);
	}
}

void LCD_Startup(){
	LCD_DDR = 0xF0 | (1<<LCD_RS) | (1<<LCD_E);
	LCD_PORT = 0;

	LCD_PORT &= ~(1<<LCD_RS);	//Poinformowanie LCD �e nadchodz�ce dane b�d� instrukcjami

	LCD_writeUpperBits(0b00110000);
	_delay_ms(5);
	LCD_writeUpperBits(0b00110000);
	_delay_ms(100);
	LCD_writeUpperBits(0b00110000);
	_delay_ms(100);
	LCD_writeUpperBits(0b00100000);
	_delay_ms(5);

	LCD_PORT |= 1<<LCD_RS;

	LCD_clear();
}

int main(void){
	LCD_Startup();

	LCD_writeText("Adam",4);
}
