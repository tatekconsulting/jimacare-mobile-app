import '../config/api_config.dart';
import '../models/job.dart';
import 'api_client.dart';
import 'job_service_mock.dart';

/// Job Service for managing job-related API calls
/// Uses mock mode if ApiConfig.useMockMode is true
class JobService {
  final ApiClient _apiClient = ApiClient();
  final JobServiceMock _mockService = JobServiceMock();
  
  bool get _useMock => ApiConfig.useMockMode;

  /// Get list of jobs
  Future<List<Job>> getJobs({
    Map<String, dynamic>? filters,
    int? page,
    int? limit,
  }) async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      return await _mockService.getJobs(filters: filters, page: page, limit: limit);
    }
    
    try {
      final queryParams = <String, dynamic>{};
      if (page != null) queryParams['page'] = page;
      if (limit != null) queryParams['limit'] = limit;
      if (filters != null) queryParams.addAll(filters);

      final response = await _apiClient.get(
        ApiConfig.jobs,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200) {
        final data = response.data;
        if (data is Map && data.containsKey('data')) {
          final jobsList = data['data'] as List;
          return jobsList.map((job) => Job.fromJson(job as Map<String, dynamic>)).toList();
        } else if (data is List) {
          return data.map((job) => Job.fromJson(job as Map<String, dynamic>)).toList();
        }
      }

      return [];
    } catch (e) {
      throw Exception('Failed to fetch jobs: $e');
    }
  }

  /// Get single job by ID
  Future<Job?> getJobById(int jobId) async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      return await _mockService.getJobById(jobId);
    }
    
    try {
      final response = await _apiClient.get('${ApiConfig.jobs}/$jobId');

      if (response.statusCode == 200) {
        return Job.fromJson(response.data as Map<String, dynamic>);
      }

      return null;
    } catch (e) {
      throw Exception('Failed to fetch job: $e');
    }
  }

  /// Search jobs
  Future<List<Job>> searchJobs(String query, {Map<String, dynamic>? filters}) async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      return await _mockService.searchJobs(query, filters: filters);
    }
    
    try {
      final queryParams = <String, dynamic>{'q': query};
      if (filters != null) queryParams.addAll(filters);

      final response = await _apiClient.get(
        ApiConfig.jobs,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200) {
        final data = response.data;
        if (data is Map && data.containsKey('data')) {
          final jobsList = data['data'] as List;
          return jobsList.map((job) => Job.fromJson(job as Map<String, dynamic>)).toList();
        } else if (data is List) {
          return data.map((job) => Job.fromJson(job as Map<String, dynamic>)).toList();
        }
      }

      return [];
    } catch (e) {
      throw Exception('Failed to search jobs: $e');
    }
  }

  /// Apply to a job
  Future<Map<String, dynamic>> applyToJob(int jobId, {String? coverLetter}) async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      return await _mockService.applyToJob(jobId, coverLetter: coverLetter);
    }
    
    try {
      final response = await _apiClient.post(
        '${ApiConfig.jobs}/$jobId/apply',
        data: {
          if (coverLetter != null) 'cover_letter': coverLetter,
        },
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Application submitted successfully',
          'data': response.data,
        };
      }

      return {
        'success': false,
        'message': 'Failed to apply to job',
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
}

