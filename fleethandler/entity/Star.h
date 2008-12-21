
#ifndef __STAR__
#define __STAR__

#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Star class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Star : public Entity {
public: 
	Star(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Stern";
		this->showCoords = true;
	}
	
	void saveData();
	
private:
	void loadData();
	
	std::string name;
	short typeId;

};

#endif
