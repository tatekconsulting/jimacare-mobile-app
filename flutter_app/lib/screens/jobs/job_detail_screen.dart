import 'package:flutter/material.dart';
import '../../models/job.dart';
import '../../services/job_service.dart';

/// Job Detail Screen
class JobDetailScreen extends StatefulWidget {
  final int jobId;

  const JobDetailScreen({super.key, required this.jobId});

  @override
  State<JobDetailScreen> createState() => _JobDetailScreenState();
}

class _JobDetailScreenState extends State<JobDetailScreen> {
  final JobService _jobService = JobService();
  Job? _job;
  bool _isLoading = true;
  String? _errorMessage;
  bool _isApplying = false;

  @override
  void initState() {
    super.initState();
    _loadJob();
  }

  Future<void> _loadJob() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final job = await _jobService.getJobById(widget.jobId);
      setState(() {
        _job = job;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = e.toString();
        _isLoading = false;
      });
    }
  }

  Future<void> _applyToJob() async {
    setState(() {
      _isApplying = true;
    });

    try {
      final result = await _jobService.applyToJob(widget.jobId);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'Application submitted'),
            backgroundColor: result['success'] == true
                ? Colors.green
                : Colors.red,
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: $e'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() {
          _isApplying = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Job Details'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _errorMessage != null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(
                        Icons.error_outline,
                        size: 48,
                        color: Colors.red,
                      ),
                      const SizedBox(height: 16),
                      Text(
                        'Error: $_errorMessage',
                        style: const TextStyle(color: Colors.red),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 16),
                      ElevatedButton(
                        onPressed: _loadJob,
                        child: const Text('Retry'),
                      ),
                    ],
                  ),
                )
              : _job == null
                  ? const Center(child: Text('Job not found'))
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Title and Status
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Expanded(
                                child: Text(
                                  _job!.title,
                                  style: const TextStyle(
                                    fontSize: 24,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                              if (_job!.status != null)
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 12,
                                    vertical: 6,
                                  ),
                                  decoration: BoxDecoration(
                                    color: _job!.status == 'open'
                                        ? Colors.green.shade100
                                        : Colors.grey.shade200,
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  child: Text(
                                    _job!.status!.toUpperCase(),
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: _job!.status == 'open'
                                          ? Colors.green.shade700
                                          : Colors.grey.shade700,
                                    ),
                                  ),
                                ),
                            ],
                          ),
                          const SizedBox(height: 16),

                          // Job Info
                          if (_job!.clientName != null) ...[
                            _InfoRow(
                              icon: Icons.person,
                              label: 'Client',
                              value: _job!.clientName!,
                            ),
                            const SizedBox(height: 8),
                          ],
                          if (_job!.location != null) ...[
                            _InfoRow(
                              icon: Icons.location_on,
                              label: 'Location',
                              value: _job!.location!,
                            ),
                            const SizedBox(height: 8),
                          ],
                          if (_job!.type != null) ...[
                            _InfoRow(
                              icon: Icons.schedule,
                              label: 'Type',
                              value: _job!.type!,
                            ),
                            const SizedBox(height: 8),
                          ],
                          if (_job!.salary != null) ...[
                            _InfoRow(
                              icon: Icons.attach_money,
                              label: 'Salary',
                              value:
                                  '${_job!.salary!.toStringAsFixed(0)}/${_job!.salaryType ?? "month"}',
                            ),
                            const SizedBox(height: 8),
                          ],
                          if (_job!.startDate != null) ...[
                            _InfoRow(
                              icon: Icons.calendar_today,
                              label: 'Start Date',
                              value: _job!.startDate!
                                  .toString()
                                  .split(' ')
                                  .first,
                            ),
                            const SizedBox(height: 8),
                          ],

                          const Divider(),
                          const SizedBox(height: 16),

                          // Description
                          const Text(
                            'Description',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            _job!.description,
                            style: const TextStyle(fontSize: 16),
                          ),

                          if (_job!.skills != null && _job!.skills!.isNotEmpty) ...[
                            const SizedBox(height: 24),
                            const Text(
                              'Required Skills',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Wrap(
                              spacing: 8,
                              runSpacing: 8,
                              children: _job!.skills!
                                  .map(
                                    (skill) => Chip(
                                      label: Text(skill),
                                      backgroundColor: Colors.green.shade50,
                                    ),
                                  )
                                  .toList(),
                            ),
                          ],

                          const SizedBox(height: 32),
                        ],
                      ),
                    ),
      bottomNavigationBar: _job != null && _job!.status == 'open'
          ? SafeArea(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: ElevatedButton(
                  onPressed: _isApplying ? null : _applyToJob,
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                  child: _isApplying
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Text(
                          'Apply for Job',
                          style: TextStyle(fontSize: 16),
                        ),
                ),
              ),
            )
          : null,
    );
  }
}

/// Info Row Widget
class _InfoRow extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _InfoRow({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Icon(icon, size: 20, color: Colors.grey),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: Colors.grey,
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontSize: 14),
          ),
        ),
      ],
    );
  }
}

