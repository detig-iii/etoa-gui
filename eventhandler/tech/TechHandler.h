
#ifndef __TECHHANDLER__
#define __TECHHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"

namespace tech
{
	class TechHandler	: EventHandler
	{
	public:
		TechHandler(mysqlpp::Connection* con)  : EventHandler(con) { this->changes_ = false; }
		void update();
		inline bool changes() { return this->changes_; }
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif
