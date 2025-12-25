import '../models/job.dart';

/// Mock Job Service for testing without backend
class JobServiceMock {
  /// Get mock jobs list
  Future<List<Job>> getJobs({
    Map<String, dynamic>? filters,
    int? page,
    int? limit,
  }) async {
    await Future.delayed(const Duration(seconds: 1));

    // Return mock jobs
    return [
      Job(
        id: 1,
        title: 'Elderly Care Assistant',
        description: 'Looking for a compassionate caregiver to assist elderly clients with daily activities, medication reminders, and companionship.',
        location: 'London, UK',
        latitude: 51.5074,
        longitude: -0.1278,
        type: 'Full-time',
        status: 'open',
        salary: 2500.0,
        salaryType: 'month',
        clientName: 'Care Home Ltd',
        createdAt: DateTime.now().subtract(const Duration(days: 2)),
        updatedAt: DateTime.now(),
        skills: ['First Aid', 'Communication', 'Patience'],
      ),
      Job(
        id: 2,
        title: 'Home Health Aide',
        description: 'Part-time position for providing personal care and support to clients in their homes.',
        location: 'Manchester, UK',
        latitude: 53.4808,
        longitude: -2.2426,
        type: 'Part-time',
        status: 'open',
        salary: 15.0,
        salaryType: 'hour',
        clientName: 'Home Care Services',
        createdAt: DateTime.now().subtract(const Duration(days: 1)),
        updatedAt: DateTime.now(),
        skills: ['Personal Care', 'Housekeeping'],
      ),
      Job(
        id: 3,
        title: 'Childcare Provider',
        description: 'Experienced childcare provider needed for after-school care and activities.',
        location: 'Birmingham, UK',
        latitude: 52.4862,
        longitude: -1.8904,
        type: 'Contract',
        status: 'open',
        salary: 20.0,
        salaryType: 'hour',
        clientName: 'Family Care',
        createdAt: DateTime.now().subtract(const Duration(hours: 12)),
        updatedAt: DateTime.now(),
        skills: ['Childcare', 'First Aid', 'Activity Planning'],
      ),
    ];
  }

  /// Get mock job by ID
  Future<Job?> getJobById(int jobId) async {
    await Future.delayed(const Duration(milliseconds: 500));

    final jobs = await getJobs();
    try {
      return jobs.firstWhere((job) => job.id == jobId);
    } catch (e) {
      return null;
    }
  }

  /// Search mock jobs
  Future<List<Job>> searchJobs(String query, {Map<String, dynamic>? filters}) async {
    await Future.delayed(const Duration(seconds: 1));

    final allJobs = await getJobs();
    if (query.isEmpty) {
      return allJobs;
    }

    final lowerQuery = query.toLowerCase();
    return allJobs.where((job) {
      return job.title.toLowerCase().contains(lowerQuery) ||
          job.description.toLowerCase().contains(lowerQuery) ||
          (job.location?.toLowerCase().contains(lowerQuery) ?? false);
    }).toList();
  }

  /// Mock apply to job
  Future<Map<String, dynamic>> applyToJob(int jobId, {String? coverLetter}) async {
    await Future.delayed(const Duration(seconds: 1));

    return {
      'success': true,
      'message': 'Application submitted successfully (Mock Mode)',
      'data': {
        'application_id': DateTime.now().millisecondsSinceEpoch,
        'job_id': jobId,
        'status': 'pending',
      },
    };
  }
}

