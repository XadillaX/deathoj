//////////////////////////////////////////////////////////////////////////  
/// COPYRIGHT NOTICE  
/// Copyright (c) 2010-2011 艾克视图(http://www.x2studio.net)
/// All rights reserved.
///
/// @file CPFactories.h
/// @brief 编译器类工厂头文件
///
/// 声明编译器类对象工厂
///  
/// @version 1.0
/// @author 朱凯迪
/// @date 2010-12-7
///  
///  
/// 修订说明：最初版本
//////////////////////////////////////////////////////////////////////////
#ifndef CPFACTORIES_H
#define CPFACTORIES_H

#include "ObjectFactory.h"
#include "NCompiler.h"

typedef ObjectFactory<NCompiler> CPFactories;

#endif
