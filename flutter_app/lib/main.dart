import 'package:flutter/material.dart';
import 'utils/app_router.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Skip Firebase initialization on web (has compatibility issues)
  // Firebase is only needed for push notifications on mobile
  // For web testing, we'll skip Firebase entirely
  
  runApp(const JimaCareApp());
}

class JimaCareApp extends StatelessWidget {
  const JimaCareApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'JimaCare',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.green),
        useMaterial3: true,
      ),
      routerConfig: AppRouter.router,
    );
  }
}
