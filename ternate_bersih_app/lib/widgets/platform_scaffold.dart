import 'package:flutter/material.dart';
import 'package:flutter/cupertino.dart';
import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;

class PlatformScaffold extends StatelessWidget {
  final Widget body;
  final String? title;
  final Widget? floatingActionButton;
  final Color? backgroundColor;
  final Widget? bottomNavigationBar;
  final List<Widget>? actions;

  const PlatformScaffold({
    super.key,
    required this.body,
    this.title,
    this.floatingActionButton,
    this.backgroundColor,
    this.bottomNavigationBar,
    this.actions,
  });

  @override
  Widget build(BuildContext context) {
    bool isIOS = !kIsWeb && Platform.isIOS;

    if (isIOS) {
      return CupertinoPageScaffold(
        backgroundColor:
            backgroundColor ?? CupertinoColors.systemGroupedBackground,
        navigationBar: title != null
            ? CupertinoNavigationBar(
                middle: Text(title!),
                trailing: actions != null
                    ? Row(mainAxisSize: MainAxisSize.min, children: actions!)
                    : null,
              )
            : null,
        child: SafeArea(
          child: Column(
            children: [
              Expanded(child: body),
              ?bottomNavigationBar,
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: title != null
          ? AppBar(title: Text(title!), actions: actions)
          : null,
      body: body,
      floatingActionButton: floatingActionButton,
      bottomNavigationBar: bottomNavigationBar,
    );
  }
}
