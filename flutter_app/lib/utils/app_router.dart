import 'package:go_router/go_router.dart';
import '../screens/auth/login_screen.dart';
import '../screens/auth/register_screen.dart';
import '../screens/home/home_screen.dart';
import '../screens/jobs/job_list_screen.dart';
import '../screens/jobs/job_detail_screen.dart';
import '../screens/profile/profile_screen.dart';
import '../services/auth_service.dart';

/// App Router Configuration
class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/login',
    routes: [
      // Auth Routes
      GoRoute(
        path: '/login',
        name: 'login',
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/register',
        name: 'register',
        builder: (context, state) => const RegisterScreen(),
      ),

      // Main App Routes
      GoRoute(
        path: '/home',
        name: 'home',
        builder: (context, state) => const HomeScreen(),
      ),
      GoRoute(
        path: '/jobs',
        name: 'jobs',
        builder: (context, state) => const JobListScreen(),
      ),
      GoRoute(
        path: '/jobs/:id',
        name: 'job-detail',
        builder: (context, state) {
          final jobId = int.parse(state.pathParameters['id']!);
          return JobDetailScreen(jobId: jobId);
        },
      ),
      GoRoute(
        path: '/profile',
        name: 'profile',
        builder: (context, state) => const ProfileScreen(),
      ),
    ],
    redirect: (context, state) async {
      final authService = AuthService();
      final isLoggedIn = await authService.isLoggedIn();
      final isLoginRoute = state.matchedLocation == '/login' || state.matchedLocation == '/register';

      // If not logged in and trying to access protected route
      if (!isLoggedIn && !isLoginRoute) {
        return '/login';
      }

      // If logged in and on login/register page
      if (isLoggedIn && isLoginRoute) {
        return '/home';
      }

      return null;
    },
  );
}

